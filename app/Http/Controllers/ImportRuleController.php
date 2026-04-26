<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ImportRule;
use App\Models\Vendor;
use App\Services\ImportRuleService;
use Illuminate\Http\Request;

class ImportRuleController extends Controller
{
    protected $importRuleService;

    public function __construct(ImportRuleService $importRuleService)
    {
        $this->importRuleService = $importRuleService;
    }

    /**
     * Display a listing of all import rules
     */
    public function index()
    {
        $rules = ImportRule::with('vendor', 'categories')
            ->orderBy('priority', 'asc')
            ->get();
        
        return view('import-rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new import rule
     */
    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('import-rules.create', compact('vendors', 'categories'));
    }

    /**
     * Store a newly created import rule in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:import_rules,name',
            'match_text' => 'required|string|max:255',
            'vendor_id' => 'required|exists:vendors,id',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id',
            'active' => 'boolean',
        ]);

        // Get the next priority value
        $maxPriority = ImportRule::max('priority') ?? 0;
        $validated['priority'] = $maxPriority + 1;
        $validated['active'] = $request->boolean('active', true);

        $rule = ImportRule::create($validated);

        // Attach categories
        if (!empty($validated['category_ids'])) {
            $rule->categories()->attach($validated['category_ids']);
        }

        // Apply the new rule to existing statements with matching narration that don't have a vendor
        $matchingStatements = \App\Models\AccountStatement::whereNull('vendor_id')
            ->where('narration', 'like', "%{$rule->match_text}%")
            ->get();
        
        $appliedCount = 0;
        foreach ($matchingStatements as $statement) {
            if ($this->importRuleService->applyRulesToStatement($statement, false)) {
                $appliedCount++;
            }
        }

        $message = "Import rule created successfully.";
        if ($appliedCount > 0) {
            $message .= " Applied to {$appliedCount} existing statement(s).";
        }

        return redirect()->route('import-rules.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified import rule
     */
    public function edit(ImportRule $importRule)
    {
        $vendors = Vendor::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $selectedCategories = $importRule->categories->pluck('id')->toArray();
        
        return view('import-rules.edit', compact('importRule', 'vendors', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified import rule in storage
     */
    public function update(Request $request, ImportRule $importRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:import_rules,name,' . $importRule->id,
            'match_text' => 'required|string|max:255',
            'vendor_id' => 'required|exists:vendors,id',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active', true);

        $importRule->update($validated);

        // Sync categories
        if (isset($validated['category_ids'])) {
            $importRule->categories()->sync($validated['category_ids']);
        } else {
            $importRule->categories()->detach();
        }

        // Apply the updated rule to existing statements with matching narration that don't have a vendor
        $matchingStatements = \App\Models\AccountStatement::whereNull('vendor_id')
            ->where('narration', 'like', "%{$importRule->match_text}%")
            ->get();
        
        $appliedCount = 0;
        foreach ($matchingStatements as $statement) {
            if ($this->importRuleService->applyRulesToStatement($statement, false)) {
                $appliedCount++;
            }
        }

        $message = "Import rule updated successfully.";
        if ($appliedCount > 0) {
            $message .= " Applied to {$appliedCount} existing statement(s).";
        }

        return redirect()->route('import-rules.index')
            ->with('success', $message);
    }

    /**
     * Create a new vendor via AJAX
     */
    public function createVendor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name',
            'description' => 'nullable|string',
            'vendor_type' => 'nullable|string|max:255',
        ]);

        $vendor = Vendor::create($validated);

        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'message' => 'Vendor created successfully'
        ]);
    }

    /**
     * Create a new category via AJAX
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'type' => 'required|string|in:vendor,account_statement',
            'color' => 'nullable|string|max:7',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Category created successfully'
        ]);
    }

    /**
     * Remove the specified import rule from storage
     */
    public function destroy(ImportRule $importRule)
    {
        $importRule->delete();
        
        return redirect()->route('import-rules.index')
            ->with('success', 'Import rule deleted successfully.');
    }

    /**
     * Show the form for applying rules to old statements
     */
    public function applyRules()
    {
        return view('import-rules.apply-rules');
    }

    /**
     * Apply import rules to all account statements
     */
    public function executeApplyRules(Request $request)
    {
        $validated = $request->validate([
            'overwrite' => 'boolean',
        ]);

        $overwrite = $validated['overwrite'] ?? false;
        $updated = $this->importRuleService->applyRulesToAllStatements($overwrite);

        return redirect()->route('import-rules.index')
            ->with('success', "Import rules applied successfully. Updated {$updated} statement(s).");
    }
}
