<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('vendor_type', $request->type);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $vendors = $query->get();
        $categories = \App\Models\Category::where('type', 'vendor')->get();

        return view('vendors.index', compact('vendors', 'categories'));
    }

    /**
     * Show the form for creating a new vendor
     */
    public function create()
    {
        $categories = \App\Models\Category::where('type', 'vendor')->get();
        return view('vendors.create', compact('categories'));
    }

    /**
     * Store a newly created vendor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:vendors|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'vendor_type' => 'required|in:service,supply,contractor,cash,other',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|exists:categories,id',
        ]);

        $vendor = Vendor::create($validated);

        // Attach categories to vendor
        if ($request->has('categories') && is_array($request->categories)) {
            $vendor->categories()->attach(array_filter($request->categories));
        }

        return redirect()->route('vendors.index')
                        ->with('success', 'Vendor created successfully.');
    }

    /**
     * Show the form for editing a vendor
     */
    public function edit(Vendor $vendor)
    {
        $categories = \App\Models\Category::where('type', 'vendor')->get();
        $vendorCategoryIds = $vendor->categories()->pluck('category_id')->toArray();
        return view('vendors.edit', compact('vendor', 'categories', 'vendorCategoryIds'));
    }

    /**
     * Update a vendor
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|unique:vendors,name,' . $vendor->id . '|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'vendor_type' => 'required|in:service,supply,contractor,cash,other',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|exists:categories,id',
        ]);

        $vendor->update($validated);

        // Sync categories for vendor
        if ($request->has('categories')) {
            $vendor->categories()->sync(array_filter($request->categories));
        } else {
            $vendor->categories()->detach();
        }

        return redirect()->route('vendors.index')
                        ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Delete a vendor
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendors.index')
                        ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Show vendor expense report
     */
    public function expenseReport()
    {
        $vendors = Vendor::with('categories', 'statements')
                        ->get()
                        ->map(function ($vendor) {
                            $vendor->total_expenses = $vendor->statements()
                                                        ->whereNotNull('withdrawal_amt')
                                                        ->sum('withdrawal_amt');
                            return $vendor;
                        })
                        ->sortByDesc('total_expenses');

        $overallTotal = $vendors->sum('total_expenses');

        return view('vendors.expense-report', compact('vendors', 'overallTotal'));
    }

    /**
     * Show vendor details with expenses
     */
    public function show(Vendor $vendor)
    {
        $statements = $vendor->statements()
                            ->whereNotNull('withdrawal_amt')
                            ->orderBy('date', 'desc')
                            ->get();

        $totalExpenses = $statements->sum('withdrawal_amt');

        return view('vendors.show', compact('vendor', 'statements', 'totalExpenses'));
    }

    /**
     * Get vendor's categories as JSON (for API)
     */
    public function getCategories(Vendor $vendor)
    {
        $categories = $vendor->categories()->get(['id', 'name', 'description']);

        return response()->json([
            'categories' => $categories,
        ]);
    }
}
