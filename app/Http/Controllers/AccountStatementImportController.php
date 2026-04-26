<?php

namespace App\Http\Controllers;

use App\Models\AccountStatement;
use App\Models\Category;
use App\Models\Flat;
use App\Models\ImportRule;
use App\Models\Vendor;
use App\Services\ImportRuleService;
use Illuminate\Http\Request;

class AccountStatementImportController extends Controller
{
    protected $importRuleService;

    public function __construct(ImportRuleService $importRuleService)
    {
        $this->importRuleService = $importRuleService;
    }
    /**
     * Show the import form
     */
    public function showForm()
    {
        return view('account-statement.import');
    }

    /**
     * Handle CSV import
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $csv = array_map('str_getcsv', file($path));

            // Remove header row
            $headers = array_shift($csv);

            $imported = 0;
            $failed = 0;
            $errors = [];

            foreach ($csv as $index => $row) {
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                try {
                    // Trim all row values
                    $row = array_map('trim', $row);

                    $date = $this->parseDate($row[0] ?? null);
                    $value_date = $this->parseDate($row[3] ?? null);

                    $narration = $row[1] ?? null;

                    // Validate that at least date or narration exists
                    if (!$date && !$narration) {
                        throw new \Exception("Row has no date and no narration");
                    }

                    $vendorId = null;
                    $categoryIds = [];

                    // Create the statement
                    $statement = AccountStatement::create([
                        'date' => $date,
                        'narration' => $narration,
                        'chq_ref_no' => $row[2] ?? null,
                        'value_date' => $value_date,
                        'withdrawal_amt' => $this->parseDecimal($row[4] ?? null),
                        'deposit_amt' => $this->parseDecimal($row[5] ?? null),
                        'closing_balance' => $this->parseDecimal($row[6] ?? null),
                    ]);

                    // Apply import rules to the statement
                    $this->importRuleService->applyRulesToStatement($statement, false);

                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if (count($errors) < 5) {
                        $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    }
                }
            }

            $message = "Imported {$imported} records successfully. {$failed} records failed.";
            if (!empty($errors)) {
                $message .= " | Errors: " . implode(" | ", $errors);
            }

            return redirect()->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error processing CSV: ' . $e->getMessage()]);
        }
    }

    /**
     * Parse date string - supports multiple formats
     */
    private function parseDate($date)
    {
        if (!$date || $date === '') {
            return null;
        }

        $date = trim($date);
        
        // Try multiple date formats
        $formats = [
            'd/m/Y',      // 31/12/2025
            'd/m/y',      // 31/12/25
            'Y/m/d',      // 2025/12/31
            'd-m-Y',      // 31-12-2025
            'd-m-y',      // 31-12-25
            'Y-m-d',      // 2025-12-31
            'd.m.Y',      // 31.12.2025
            'd.m.y',      // 31.12.25
        ];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) {
                // Try next format
            }
        }

        // If no format matches, return null
        return null;
    }

    /**
     * Parse decimal value - handles various formats
     */
    private function parseDecimal($value)
    {
        if (!$value || $value === '' || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // Remove common thousands separators and convert to decimal
        $value = str_replace([',', ' '], '', $value);

        // Handle Indian numbering format (1,00,000 -> 100000)
        if (preg_match('/,(?=\d{2}$)/', $value)) {
            // This is Indian format with comma before last 2 digits
            $value = str_replace(',', '', $value);
        }

        // Convert to float
        $decimal = (float) $value;

        // Return null if conversion resulted in 0 and original wasn't 0-like
        if ($decimal == 0 && !in_array(strtolower(trim($value)), ['0', '0.0', '0.00', '-', '--'])) {
            return null;
        }

        return $decimal;
    }

    /**
     * Display a listing of all statements with filters
     */
    public function index(Request $request)
{
    $query = AccountStatement::query();

    // Filter by tagged flat
    if ($request->filled('flat_id')) {
        $query->where('flat_id', $request->flat_id);
    }

    // Filter for blank/not blank vendors (takes precedence over specific vendor_id filter)
    if ($request->filled('vendor_filter')) {
        if ($request->vendor_filter === 'blank') {
            $query->whereNull('vendor_id');
        } elseif ($request->vendor_filter === 'not_blank') {
            $query->whereNotNull('vendor_id');
        }
    } elseif ($request->filled('vendor_id')) {
        // Filter by specific tagged vendor (only if vendor_filter is not set)
        $query->where('vendor_id', $request->vendor_id);
    }

    // Filter by tagged/untagged
    if ($request->filled('tagged')) {
        if ($request->tagged === 'tagged') {
            $query->where(function ($q) {
                $q->whereNotNull('flat_id')
                  ->orWhereNotNull('vendor_id');
            });
        } elseif ($request->tagged === 'untagged') {
            $query->whereNull('flat_id')->whereNull('vendor_id');
        }
    }

    // Withdrawal/deposit filters
    $showWithdrawal = $request->filled('withdrawal_filter');
    $showDeposit = $request->filled('deposit_filter');

    if ($showWithdrawal && !$showDeposit) {
        $query->whereNotNull('withdrawal_amt')
              ->whereNull('deposit_amt');
    } elseif ($showDeposit && !$showWithdrawal) {
        $query->whereNotNull('deposit_amt')
              ->whereNull('withdrawal_amt');
    }

    // Search in narration
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('narration', 'like', "%{$search}%");
    }

    // Sort
    $allowedSortColumns = ['date', 'narration', 'withdrawal_amt', 'deposit_amt'];
    $sortBy = in_array($request->get('sort'), $allowedSortColumns, true) ? $request->get('sort') : 'date';
    $sortOrder = $request->get('order') === 'asc' ? 'asc' : 'desc';
    $query->orderBy($sortBy, $sortOrder);

    $statements = $query->get();
    $flats = Flat::orderBy('flat_number')->get();
    $vendors = Vendor::orderBy('name')->get();

    return view('account-statement.index', compact('statements', 'flats', 'vendors'));
}


    /**
     * Tag a statement with a flat
     */
    public function tagFlat(Request $request, $statementId)
    {
        $validated = $request->validate([
            'flat_id' => 'required|exists:flats,id',
        ]);

        $statement = AccountStatement::findOrFail($statementId);
        $statement->update(['flat_id' => $validated['flat_id']]);

        return redirect()->back()->with('success', 'Statement tagged to flat successfully.');
    }

    /**
     * Untag a statement
     */
    public function untagFlat($statementId)
    {
        $statement = AccountStatement::findOrFail($statementId);
        $statement->update(['flat_id' => null]);

        return redirect()->back()->with('success', 'Statement untagged successfully.');
    }

    /**
     * Suggest a flat based on statement narration
     */
    public function suggestFlat($statementId)
    {
        $statement = AccountStatement::findOrFail($statementId);
        $narration = $statement->narration ?? '';

        // Try to find flat by flat number in narration
        preg_match('/\b(\d{3,4})\b/', $narration, $matches);
        if (!empty($matches[1])) {
            $flatNumber = $matches[1];
            $flat = Flat::where('flat_number', $flatNumber)->first();
            if ($flat) {
                return response()->json(['flat' => $flat]);
            }
        }

        // Try to find flat by owner name
        $flat = Flat::where('owner_name', 'like', "%{$narration}%")->first();
        if ($flat) {
            return response()->json(['flat' => $flat]);
        }

        return response()->json(['flat' => null]);
    }

    /**
     * Tag a statement with a vendor
     */
    public function tagVendor(Request $request, $statementId)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        $statement = AccountStatement::findOrFail($statementId);
        $statement->update(['vendor_id' => $validated['vendor_id']]);

        return redirect()->back()->with('success', 'Statement tagged to vendor successfully.');
    }

    /**
     * Untag vendor from a statement
     */
    public function untagVendor($statementId)
    {
        $statement = AccountStatement::findOrFail($statementId);
        $statement->update(['vendor_id' => null]);

        return redirect()->back()->with('success', 'Vendor tag removed successfully.');
    }

    /**
     * Create a new vendor and tag a statement to it
     */
    public function createAndTagVendor(Request $request, $statementId)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|unique:vendors,name|string|max:255',
            'vendor_type' => 'required|in:service,supply,contractor,cash,other',
            'contact_person' => 'nullable|string|max:255',
        ]);

        // Create the vendor
        $vendor = Vendor::create([
            'name' => $validated['vendor_name'],
            'vendor_type' => $validated['vendor_type'],
            'contact_person' => $validated['contact_person'] ?? null,
        ]);

        // Tag the statement
        $statement = AccountStatement::findOrFail($statementId);
        $statement->update(['vendor_id' => $vendor->id]);

        return redirect()->back()->with('success', 'Vendor created and statement tagged successfully.');
    }

    /**
     * Bulk tag all statements matching search to a vendor
     */
    public function bulkTagVendor(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'search' => 'required|string',
        ]);

        // Find all statements matching the search term
        $statements = AccountStatement::where('narration', 'like', "%{$validated['search']}%")->get();

        if ($statements->isEmpty()) {
            return redirect()->back()->with('error', 'No statements found matching the search.');
        }

        // Tag all statements to the vendor
        $count = $statements->count();
        foreach ($statements as $statement) {
            $statement->update(['vendor_id' => $validated['vendor_id']]);
        }

        return redirect()->back()->with('success', "Successfully tagged {$count} statement(s) to the vendor!");
    }
}
