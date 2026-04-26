<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display all vendor categories
     */
    public function index()
    {
        $vendorCategories = Category::where('type', 'vendor')
                                   ->orderBy('name')
                                   ->get();
        
        return view('categories.index', compact('vendorCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:vendor,account_statement',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Get categories by type (API endpoint)
     */
    public function getByType(Request $request)
    {
        $type = $request->query('type', 'vendor');

        $categories = Category::where('type', $type)
                             ->orderBy('name')
                             ->get();

        return response()->json($categories);
    }

    /**
     * Show the form for editing a category
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete the specified category
     */
    public function destroy(Category $category)
    {
        // Detach from vendors and account statements
        $category->vendors()->detach();
        $category->accountStatements()->detach();

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Category deleted successfully.');
    }
}

