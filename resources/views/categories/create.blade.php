@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:underline">&larr; Back to Categories</a>
    </div>

    <div class="bg-white rounded-lg shadow p-8 max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Add New Category</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">
                <h3 class="font-medium mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                <p class="text-gray-500 text-sm mt-1">Optional: Add details about this category</p>
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <div class="flex gap-4 items-center">
                    <input type="color" id="color" name="color" value="{{ old('color', '#52C966') }}" 
                        class="h-12 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" id="colorHex" value="{{ old('color', '#52C966') }}" readonly
                        class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm flex-1">
                </div>
                <p class="text-gray-500 text-sm mt-1">Choose a color for better visual organization</p>
            </div>

            <input type="hidden" name="type" value="vendor">

            <div class="flex gap-3 justify-end pt-6 border-t">
                <a href="{{ route('categories.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('color')?.addEventListener('change', function() {
    document.getElementById('colorHex').value = this.value;
});
</script>
@endsection
