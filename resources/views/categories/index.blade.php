@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Expense Categories</h1>
        <a href="{{ route('categories.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
            + Add Category
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if ($vendorCategories->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg mb-4">No categories found.</p>
            <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:underline font-medium">
                Create your first category
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Category Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Color</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Used in Vendors</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Used in Transactions</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendorCategories as $category)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-3">
                                @if ($category->color)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded" style="background-color: {{ $category->color }};"></div>
                                        <span class="text-xs text-gray-500">{{ $category->color }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if ($category->vendors->count() > 0)
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-gray-100 text-gray-600
                                    @endif
                                ">
                                    {{ $category->vendors->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if ($category->accountStatements->count() > 0)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-600
                                    @endif
                                ">
                                    {{ $category->accountStatements->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Delete this category?');">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
