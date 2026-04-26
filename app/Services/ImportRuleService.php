<?php

namespace App\Services;

use App\Models\AccountStatement;
use App\Models\ImportRule;

class ImportRuleService
{
    /**
     * Find the first matching rule for a narration by priority
     *
     * @param string $narration
     * @return ImportRule|null
     */
    public function findMatchingRule(string $narration): ?ImportRule
    {
        $narrationLower = strtolower($narration);
        
        return ImportRule::where('active', true)
            ->orderBy('priority', 'asc')
            ->get()
            ->first(function (ImportRule $rule) use ($narrationLower) {
                return strpos($narrationLower, strtolower($rule->match_text)) !== false;
            });
    }

    /**
     * Apply rules to a single account statement
     *
     * @param AccountStatement $statement
     * @param bool $overwrite Whether to overwrite existing vendor/category assignments
     * @return bool Whether any rule was applied
     */
    public function applyRulesToStatement(AccountStatement $statement, bool $overwrite = false): bool
    {
        // Skip if statement already has a vendor and we're not overwriting
        if ($statement->vendor_id && !$overwrite) {
            return false;
        }

        $rule = $this->findMatchingRule($statement->narration);

        if (!$rule) {
            return false;
        }

        // Apply vendor
        $statement->vendor_id = $rule->vendor_id;
        $statement->save();

        // Apply categories
        if ($rule->categories()->exists()) {
            $statement->categories()->sync($rule->categories->pluck('id'));
        } else {
            // Detach all categories if rule has none
            $statement->categories()->detach();
        }

        return true;
    }

    /**
     * Apply rules to all account statements (backfill)
     *
     * @param bool $overwrite Whether to overwrite existing vendor/category assignments
     * @return int Number of statements updated
     */
    public function applyRulesToAllStatements(bool $overwrite = false): int
    {
        $query = AccountStatement::query();

        // If not overwriting, only process statements without a vendor
        if (!$overwrite) {
            $query->whereNull('vendor_id');
        }

        $statements = $query->get();
        $updated = 0;

        foreach ($statements as $statement) {
            if ($this->applyRulesToStatement($statement, $overwrite)) {
                $updated++;
            }
        }

        return $updated;
    }
}
