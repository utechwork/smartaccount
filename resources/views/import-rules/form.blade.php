@csrf

<div class="mb-4">
    <div class="flex justify-between items-center mb-2">
        <label for="name" class="block text-gray-700 font-bold">Rule Name</label>
        <div class="flex gap-2">
            <button type="button" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" onclick="cleanRuleName()" title="Remove numbers and special characters, keep only letters and spaces">
                🧹 Clean
            </button>
            <button type="button" class="text-xs bg-purple-500 text-white px-2 py-1 rounded hover:bg-purple-600" onclick="copyToMatchText()" title="Copy cleaned name to Match Text field">
                📋 Copy to Match Text
            </button>
        </div>
    </div>
    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded @error('name') border-red-500 @enderror" 
           value="{{ old('name', $importRule->name ?? request('match_text', '')) }}" required>
    @error('name')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="match_text" class="block text-gray-700 font-bold mb-2">Match Text</label>
    <input type="text" name="match_text" id="match_text" class="w-full px-3 py-2 border border-gray-300 rounded @error('match_text') border-red-500 @enderror" 
           placeholder="Text to match in narration (case-insensitive)" value="{{ old('match_text', $importRule->match_text ?? request('match_text', '')) }}" required>
    <small class="text-gray-500">The text will be matched case-insensitively within the narration field.</small>
    @error('match_text')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <div class="flex justify-between items-center mb-2">
        <label for="vendor_search" class="block text-gray-700 font-bold">Vendor *</label>
        <button type="button" class="text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600" onclick="openVendorModal()">
            + Create New Vendor
        </button>
    </div>
    <input type="text" id="vendor_search" placeholder="Search vendors..." class="w-full px-3 py-2 border border-gray-300 rounded @error('vendor_id') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="filterVendorDropdown()">
    <div id="vendorDropdown" class="hidden border border-t-0 border-gray-300 rounded-b bg-white shadow-lg z-10 max-h-48 overflow-y-auto">
        @foreach ($vendors as $vendor)
            <div class="vendor-option px-3 py-2 cursor-pointer hover:bg-blue-100" data-vendor-id="{{ $vendor->id }}" data-vendor-name="{{ $vendor->name }}" style="cursor: pointer;">
                {{ $vendor->name }}
            </div>
        @endforeach
    </div>
    <input type="hidden" id="vendor_id_hidden" name="vendor_id" value="{{ old('vendor_id', $importRule->vendor_id ?? '') }}">
    <span id="selected_vendor_name" class="text-sm text-gray-600 mt-1 block"></span>
    @error('vendor_id')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<script>
// Initialize vendor options with event listeners
window.addEventListener('load', function() {
    const vendorOptions = document.querySelectorAll('.vendor-option');
    vendorOptions.forEach(option => {
        option.addEventListener('click', function() {
            const vendorId = parseInt(this.dataset.vendorId);
            const vendorName = this.dataset.vendorName;
            selectVendor(vendorId, vendorName);
        });
    });
    
    // Set initial selected vendor display if editing
    const vendorId = document.getElementById('vendor_id_hidden').value;
    if (vendorId) {
        const selectedOption = document.querySelector(`.vendor-option[data-vendor-id="${vendorId}"]`);
        if (selectedOption) {
            const vendorName = selectedOption.dataset.vendorName;
            document.getElementById('vendor_search').value = vendorName;
            document.getElementById('selected_vendor_name').textContent = 'Selected: ' + vendorName;
        }
    }
});

// Clean Rule Name: Remove numbers and special characters, keep only letters and spaces
function cleanRuleName() {
    const nameInput = document.getElementById('name');
    const currentValue = nameInput.value;
    
    // Remove numbers and special characters, keep only letters, spaces, and hyphens
    const cleanedValue = currentValue.replace(/[^a-zA-Z\s\-]/g, '').trim();
    
    nameInput.value = cleanedValue;
}

// Copy cleaned Rule Name to Match Text
function copyToMatchText() {
    const nameInput = document.getElementById('name');
    const matchTextInput = document.getElementById('match_text');
    
    let cleanedValue = nameInput.value;
    
    // If Rule Name has numbers or special chars, clean it first
    if (/[^a-zA-Z\s\-]/.test(cleanedValue)) {
        cleanedValue = cleanedValue.replace(/[^a-zA-Z\s\-]/g, '').trim();
    }
    
    if (!cleanedValue) {
        alert('Rule Name is empty or has no valid characters');
        return;
    }
    
    matchTextInput.value = cleanedValue;
    alert('Copied to Match Text: ' + cleanedValue);
}

function filterVendorDropdown() {
    const searchTerm = document.getElementById('vendor_search').value.toLowerCase();
    const dropdown = document.getElementById('vendorDropdown');
    const options = document.querySelectorAll('.vendor-option');
    
    dropdown.classList.remove('hidden');
    
    let hasVisibleOptions = false;
    options.forEach(option => {
        const vendorText = option.textContent.toLowerCase();
        if (vendorText.includes(searchTerm)) {
            option.style.display = 'block';
            hasVisibleOptions = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    if (!hasVisibleOptions) {
        dropdown.classList.add('hidden');
    }
}

function selectVendor(vendorId, vendorName) {
    document.getElementById('vendor_id_hidden').value = vendorId;
    document.getElementById('vendor_search').value = vendorName;
    document.getElementById('vendor_search').classList.remove('border-red-500');
    document.getElementById('vendor_search').classList.add('border-green-500', 'bg-green-50');
    document.getElementById('selected_vendor_name').textContent = '✓ Selected: ' + vendorName;
    document.getElementById('selected_vendor_name').classList.add('text-green-600', 'font-semibold');
    document.getElementById('vendorDropdown').classList.add('hidden');
    console.log('Vendor selected:', {vendorId, vendorName});
}

function addVendorToDropdown(vendor) {
    const dropdown = document.getElementById('vendorDropdown');
    const newOption = document.createElement('div');
    newOption.className = 'vendor-option px-3 py-2 cursor-pointer hover:bg-blue-100';
    newOption.textContent = vendor.name;
    newOption.style.cursor = 'pointer';
    
    // Use data attributes to store vendor ID and name
    newOption.dataset.vendorId = vendor.id;
    newOption.dataset.vendorName = vendor.name;
    
    // Add click event listener
    newOption.addEventListener('click', function() {
        selectVendor(parseInt(this.dataset.vendorId), this.dataset.vendorName);
    });
    
    dropdown.appendChild(newOption);
    console.log('Added vendor to dropdown:', vendor);
}

function openVendorModal() {
    const vendorModal = document.getElementById('vendorModal');
    if (vendorModal) {
        vendorModal.classList.remove('hidden');
        document.getElementById('vendorForm').reset();
    } else {
        console.error('Vendor modal not found');
    }
}

function closeVendorModal() {
    const vendorModal = document.getElementById('vendorModal');
    if (vendorModal) {
        vendorModal.classList.add('hidden');
    }
}

function copyRuleNameToVendor() {
    const ruleName = document.getElementById('name').value.trim();
    const cleanedName = ruleName.replace(/[^a-zA-Z\s\-]/g, '').trim();
    
    if (!cleanedName) {
        alert('Rule Name is empty or has no valid characters');
        return;
    }
    
    document.getElementById('vendorName').value = cleanedName;
    document.getElementById('vendorName').focus();
}

function createVendor() {
    const nameInput = document.getElementById('vendorName');
    const name = nameInput.value.trim();
    const description = document.getElementById('vendorDescription').value.trim();
    const vendorType = document.getElementById('vendorType').value.trim();
    
    if (!name) {
        nameInput.classList.add('border-red-500');
        alert('Vendor name is required');
        nameInput.focus();
        return;
    }

    const csrfToken = document.querySelector('input[name="_token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Security token not found. Please refresh the page.');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('vendor_type', vendorType);
    formData.append('_token', csrfToken.value);

    console.log('Creating vendor:', {name, description, vendorType});

    fetch('{{ route("import-rules.create-vendor") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success && data.vendor) {
            addVendorToDropdown(data.vendor);
            selectVendor(data.vendor.id, data.vendor.name);
            closeVendorModal();
            console.log('Vendor created and selected successfully');
        } else {
            const errorMsg = data.message || 'Failed to create vendor';
            console.error('Error:', errorMsg);
            alert('Error: ' + errorMsg);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error creating vendor: ' + error.message);
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('vendorDropdown');
    const search = document.getElementById('vendor_search');
    if (dropdown && search && !dropdown.contains(e.target) && e.target !== search) {
        dropdown.classList.add('hidden');
    }
});

// Show dropdown on focus
document.getElementById('vendor_search').addEventListener('focus', function() {
    document.getElementById('vendorDropdown').classList.remove('hidden');
});
</script>

<div class="mb-4">
    <div class="flex justify-between items-center mb-2">
        <label for="category_ids" class="block text-gray-700 font-bold">Categories (Optional)</label>
        <button type="button" class="text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600" onclick="openCategoryModal()">
            + Create New Category
        </button>
    </div>
    <input type="text" id="categorySearchInput" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" oninput="filterImportRuleCategories()">
    <div id="categoriesContainer" class="border border-gray-300 rounded p-3 bg-gray-50" style="max-height: 300px; overflow-y: auto;">
        @forelse ($categories as $category)
            <div class="form-check mb-2 category-item">
                <input class="form-check-input" type="checkbox" name="category_ids[]" id="category_{{ $category->id }}" 
                       value="{{ $category->id }}"
                       @checked(in_array($category->id, old('category_ids', $selectedCategories ?? [])))>
                <label class="form-check-label" for="category_{{ $category->id }}">
                    {{ $category->name }}
                </label>
            </div>
        @empty
            <p class="text-gray-500 italic">No categories available. <a href="/categories" class="text-blue-600">Create one</a></p>
        @endforelse
    </div>
    @error('category_ids')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<script>
function filterImportRuleCategories() {
    const searchTerm = document.getElementById('categorySearchInput').value.toLowerCase();
    const categoryItems = document.querySelectorAll('.category-item');
    
    categoryItems.forEach(item => {
        const categoryText = item.textContent.toLowerCase();
        if (categoryText.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function addCategoryToList(category) {
    const container = document.getElementById('categoriesContainer');
    const newItem = document.createElement('div');
    newItem.className = 'form-check mb-2 category-item';
    newItem.innerHTML = `
        <input class="form-check-input" type="checkbox" name="category_ids[]" id="category_${category.id}" 
               value="${category.id}" checked>
        <label class="form-check-label" for="category_${category.id}">
            ${category.name}
        </label>
    `;
    container.appendChild(newItem);
}

function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
    document.getElementById('categoryForm').reset();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function createCategory() {
    const name = document.getElementById('categoryName').value.trim();
    const description = document.getElementById('categoryDescription').value.trim();
    const type = document.getElementById('categoryType').value;
    const color = document.getElementById('categoryColor').value.trim();
    
    if (!name) {
        alert('Category name is required');
        return;
    }
    
    if (!type) {
        alert('Category type is required');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('type', type);
    formData.append('color', color);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    fetch('{{ route("import-rules.create-category") }}', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addCategoryToList(data.category);
            closeCategoryModal();
        } else {
            alert('Error: ' + (data.message || 'Failed to create category'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating category');
    });
}
</script>

<div class="mb-4">
    <label for="active" class="flex items-center">
        <input type="checkbox" name="active" id="active" value="1" class="form-check-input mr-2"
               @checked(old('active', $importRule->active ?? true))>
        <span class="text-gray-700 font-bold">Active</span>
    </label>
    <small class="text-gray-500">Inactive rules will not be applied during import.</small>
</div>

<!-- Vendor Creation Modal -->
<div id="vendorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Vendor</h3>
        <form id="vendorForm">
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label for="vendorName" class="block text-gray-700 font-bold">Vendor Name *</label>
                    <button type="button" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" onclick="copyRuleNameToVendor()" title="Copy cleaned name from Rule Name">
                        📋 Copy Name
                    </button>
                </div>
                <input type="text" id="vendorName" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="vendorDescription" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea id="vendorDescription" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label for="vendorType" class="block text-gray-700 font-bold mb-2">Vendor Type</label>
                <input type="text" id="vendorType" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeVendorModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="button" onclick="createVendor()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create Vendor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Category Creation Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Category</h3>
        <form id="categoryForm">
            <div class="mb-4">
                <label for="categoryName" class="block text-gray-700 font-bold mb-2">Category Name *</label>
                <input type="text" id="categoryName" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="categoryDescription" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea id="categoryDescription" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"></textarea>
            </div>
            <div class="mb-4">
                <label for="categoryType" class="block text-gray-700 font-bold mb-2">Type *</label>
                <select id="categoryType" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Type</option>
                    <option value="vendor">Vendor</option>
                    <option value="account_statement">Account Statement</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="categoryColor" class="block text-gray-700 font-bold mb-2">Color (Optional)</label>
                <input type="color" id="categoryColor" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="button" onclick="createCategory()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Close modals when clicking outside -->
<script>
document.getElementById('vendorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVendorModal();
    }
});

document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

// Form validation on submit
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Only target forms that have the vendor_id field
        if (form.querySelector('input[name="vendor_id"]')) {
            form.addEventListener('submit', function(e) {
                const vendorId = document.getElementById('vendor_id_hidden').value.trim();
                const ruleName = document.getElementById('name').value.trim();
                const matchText = document.getElementById('match_text').value.trim();
                
                console.log('Form submission validation:', {vendorId, ruleName, matchText});
                
                if (!ruleName) {
                    e.preventDefault();
                    alert('Please enter a Rule Name');
                    document.getElementById('name').focus();
                    return false;
                }
                
                if (!matchText) {
                    e.preventDefault();
                    alert('Please enter Match Text');
                    document.getElementById('match_text').focus();
                    return false;
                }
                
                if (!vendorId) {
                    e.preventDefault();
                    document.getElementById('vendor_id_hidden').classList.add('border-red-500');
                    document.getElementById('vendor_search').classList.add('border-red-500');
                    alert('Please select a Vendor before creating the rule');
                    document.getElementById('vendor_search').focus();
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
                return true;
            });
        }
    });
});
</script>
