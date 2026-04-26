<?php

namespace App\Http\Controllers;

use App\Models\AccountStatement;
use App\Models\Vendor;
use App\Models\Flat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfExportController extends Controller
{
    /**
     * Export account statements to PDF
     */
    public function accountStatements(Request $request)
    {
        // Start with base query and eager load relationships
        $query = AccountStatement::with(['flat', 'vendor', 'categories']);

        // Apply search filter first
        if ($request->filled('search')) {
            $query->where('narration', 'like', '%' . $request->search . '%');
        }

        // Apply flat filter
        if ($request->filled('flat_id')) {
            $query->where('flat_id', $request->flat_id);
        }

        // Apply vendor filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Apply tagged/untagged filter
        if ($request->filled('tagged')) {
            if ($request->tagged === 'tagged') {
                $query->where(function ($q) {
                    $q->whereNotNull('flat_id')
                      ->orWhereNotNull('vendor_id');
                });
            } elseif ($request->tagged === 'untagged') {
                $query->whereNull('flat_id')
                      ->whereNull('vendor_id');
            }
        }

        // Apply withdrawal/deposit filters
        if ($request->filled('withdrawal_filter') && !$request->filled('deposit_filter')) {
            // Only show withdrawals
            $query->whereNotNull('withdrawal_amt');
        } elseif ($request->filled('deposit_filter') && !$request->filled('withdrawal_filter')) {
            // Only show deposits
            $query->whereNotNull('deposit_amt');
        }
        // If both or neither are filled, show all

        // Execute query
        $statements = $query->orderBy('date', 'desc')->get();

        // Debug logging
        \Log::info('PDF Export - Account Statements', [
            'count' => count($statements),
            'query_sql' => $query->toSql(),
            'first_statement' => $statements->first() ? $statements->first()->toArray() : null,
        ]);

        $data = [
            'statements' => $statements,
            'flats' => Flat::orderBy('flat_number')->get(),
            'vendors' => Vendor::orderBy('name')->get(),
        ];

        $pdf = Pdf::loadView('pdf.account-statements', $data);
        return $pdf->download('account-statements-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export vendor expense report to PDF
     */
    public function vendorExpenseReport()
    {
        $vendors = Vendor::with('categories')
            ->withSum('statements', 'withdrawal_amt')
            ->get()
            ->map(function ($vendor) {
                $vendor->total_expenses = $vendor->statements_sum_withdrawal_amt ?? 0;
                return $vendor;
            });

        $overallTotal = $vendors->sum('total_expenses');

        $data = [
            'vendors' => $vendors,
            'overallTotal' => $overallTotal,
        ];

        $pdf = Pdf::loadView('pdf.vendor-expense-report', $data);
        return $pdf->download('vendor-expense-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export flat details to PDF
     */
    public function flatDetails($flatId)
    {
        $flat = Flat::with('accountStatements')->findOrFail($flatId);

        $data = [
            'flat' => $flat,
        ];

        $pdf = Pdf::loadView('pdf.flat-details', $data);
        return $pdf->download('flat-' . $flat->flat_number . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export vendor details to PDF
     */
    public function vendorDetails($vendorId)
    {
        $vendor = Vendor::with(['categories', 'statements'])->findOrFail($vendorId);
        $totalExpenses = $vendor->statements->sum('withdrawal_amt');

        $data = [
            'vendor' => $vendor,
            'totalExpenses' => $totalExpenses,
        ];

        $pdf = Pdf::loadView('pdf.vendor-details', $data);
        return $pdf->download('vendor-' . $vendor->name . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
