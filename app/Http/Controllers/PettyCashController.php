<?php

namespace App\Http\Controllers;

use App\Models\PettyCash;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{
    /**
     * Display the petty cash expense report
     */
    public function index(Request $request)
    {
        $query = PettyCash::query();

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('expense_category', $request->category);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $pettyDetails = $query->orderBy('date', 'desc')->get();

        // Calculate totals
        $totalAmount = PettyCash::where('is_withdrawal', false)->sum('amount');
        $totalExpensePaid = $pettyDetails->sum('expense_paid');
        $totalWithdrawals = PettyCash::where('is_withdrawal', true)->sum('amount');

        // Group by category for summary
        $categoryTotals = $pettyDetails->groupBy('expense_category')->map(function ($group) {
            return $group->sum('expense_paid');
        });

        return view('petty-cash.index', compact(
            'pettyDetails',
            'totalAmount',
            'totalExpensePaid',
            'totalWithdrawals',
            'categoryTotals'
        ));
    }

    /**
     * Show the form for creating a new petty cash entry
     */
    public function create()
    {
        return view('petty-cash.create');
    }

    /**
     * Store a newly created petty cash entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'availability' => 'nullable|in:Yes,No',
            'expense_type' => 'nullable|string',
            'expense_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'cleared_by' => 'nullable|string',
            'vendor_name' => 'nullable|string',
            'expense_details' => 'nullable|string',
            'remark' => 'nullable|string',
            'expense_category' => 'nullable|in:Maintenance,Miscellaneous',
            'is_withdrawal' => 'boolean',
        ]);

        PettyCash::create($validated);

        return redirect()->route('petty-cash.index')->with('success', 'Petty cash entry created successfully.');
    }

    /**
     * Display the specified petty cash entry
     */
    public function show(PettyCash $pettyCash)
    {
        return view('petty-cash.show', compact('pettyCash'));
    }

    /**
     * Show the form for editing the specified petty cash entry
     */
    public function edit(PettyCash $pettyCash)
    {
        return view('petty-cash.edit', compact('pettyCash'));
    }

    /**
     * Update the specified petty cash entry
     */
    public function update(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'availability' => 'nullable|in:Yes,No',
            'expense_type' => 'nullable|string',
            'expense_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'cleared_by' => 'nullable|string',
            'vendor_name' => 'nullable|string',
            'expense_details' => 'nullable|string',
            'remark' => 'nullable|string',
            'expense_category' => 'nullable|in:Maintenance,Miscellaneous',
            'is_withdrawal' => 'boolean',
        ]);

        $pettyCash->update($validated);

        return redirect()->route('petty-cash.index')->with('success', 'Petty cash entry updated successfully.');
    }

    /**
     * Remove the specified petty cash entry
     */
    public function destroy(PettyCash $pettyCash)
    {
        $pettyCash->delete();

        return redirect()->route('petty-cash.index')->with('success', 'Petty cash entry deleted successfully.');
    }
}
