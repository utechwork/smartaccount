@extends('layouts.app')

@section('title', 'Add New Vendor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-8">Add New Vendor</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('vendors.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Type *</label>
                <select name="vendor_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('vendor_type') border-red-500 @enderror">
                    <option value="">-- Select Type --</option>
                    <option value="service" {{ old('vendor_type') === 'service' ? 'selected' : '' }}>Service</option>
                    <option value="supply" {{ old('vendor_type') === 'supply' ? 'selected' : '' }}>Supply</option>
                    <option value="contractor" {{ old('vendor_type') === 'contractor' ? 'selected' : '' }}>Contractor</option>
                    <option value="cash" {{ old('vendor_type') === 'cash' ? 'selected' : '' }}>Cash Expense</option>
                    <option value="other" {{ old('vendor_type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('vendor_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Categories</label>
                    <button type="button" onclick="openAddCategoryModal()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Add New Category
                    </button>
                </div>
                <input type="text" id="categorySearchInput" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" oninput="filterVendorCategories()">
                <div id="categories-container" class="space-y-2 border border-gray-300 rounded-lg p-3 bg-gray-50" style="max-height: 300px; overflow-y: auto;">
                    @forelse($categories as $category)
                        <label class="flex items-center category-item">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                   {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                            @if($category->description)
                                <span class="ml-2 text-xs text-gray-500">({{ $category->description }})</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">No categories available. <button type="button" onclick="openAddCategoryModal()" class="text-blue-600 hover:underline">Create one</button></p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" placeholder="Notes about this vendor..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('vendors.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Create Vendor</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold mb-4">Add New Category</h2>
        <form id="addCategoryForm">
            @csrf
            <input type="hidden" name="type" value="vendor">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <input type="color" name="color" value="#808080" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
            </div>

            <div id="categoryError" class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 hidden"></div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddCategoryModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Add Category</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('hidden');
}

function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.add('hidden');
    document.getElementById('addCategoryForm').reset();
    document.getElementById('categoryError').classList.add('hidden');
}

document.getElementById('addCategoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("categories.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        const data = await response.json();

        if (!response.ok) {
            const errorMsg = data.message || Object.values(data.errors || {}).flat().join(', ');
            document.getElementById('categoryError').textContent = errorMsg;
            document.getElementById('categoryError').classList.remove('hidden');
            return;
        }

        // Add the new category to the list
        const category = data.category;
        const container = document.getElementById('categories-container');
        
        // Remove empty message if exists
        const emptyMsg = container.querySelector('.text-sm.text-gray-500');
        if (emptyMsg) emptyMsg.remove();

        // Add new category checkbox
        const label = document.createElement('label');
        label.className = 'flex items-center';
        label.innerHTML = `
            <input type="checkbox" name="categories[]" value="${category.id}" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
            <span class="ml-3 text-sm text-gray-700">${category.name}</span>
            ${category.description ? `<span class="ml-2 text-xs text-gray-500">(${category.description})</span>` : ''}
        `;
        container.appendChild(label);

        closeAddCategoryModal();
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('categoryError').textContent = 'An error occurred. Please try again.';
        document.getElementById('categoryError').classList.remove('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('addCategoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddCategoryModal();
    }
});

function filterVendorCategories() {
    const searchTerm = document.getElementById('categorySearchInput').value.toLowerCase();
    const categoryItems = document.querySelectorAll('.category-item');
    
    categoryItems.forEach(item => {
        const categoryText = item.textContent.toLowerCase();
        if (categoryText.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection
