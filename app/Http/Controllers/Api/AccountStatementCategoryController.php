<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountStatement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountStatementCategoryController extends Controller
{
    /**
     * Get categories for an account statement
     */
    public function getCategories(AccountStatement $statement)
    {
        $categories = $statement->categories()->pluck('category_id')->toArray();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Sync categories for an account statement
     */
    public function syncCategories(Request $request, AccountStatement $statement)
    {
        $validated = $request->validate([
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $statement->categories()->sync($validated['category_ids'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Categories updated successfully',
        ]);
    }

    /**
     * Get expense details for an account statement
     */
    public function getExpense(AccountStatement $statement)
    {
        return response()->json([
            'expense_details' => $statement->expense_details,
            'remark' => $statement->remark,
        ]);
    }

    /**
     * Update expense details for an account statement
     */
    public function updateExpense(Request $request, AccountStatement $statement)
    {
        $validated = $request->validate([
            'expense_details' => 'nullable|string|max:1000',
            'remark' => 'nullable|string|max:1000',
        ]);

        $statement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense details updated successfully',
        ]);
    }
}

