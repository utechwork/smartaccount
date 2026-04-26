@extends('layouts.app')

@section('title', 'Account Statements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Account Statements</h1>
        <div class="flex gap-3">
            <a href="{{ route('account-statement.export.pdf') }}?{{ http_build_query(request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
                📄 Export PDF
            </a>
            <a href="{{ route('account-statement.import.form') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Import New
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('account-statement.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Narration</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Flat</label>
                    <select name="flat_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Flats</option>
                        @foreach ($flats as $flat)
                            <option value="{{ $flat->id }}" {{ request('flat_id') == $flat->id ? 'selected' : '' }}>
                                {{ $flat->flat_number }} - {{ $flat->owner_name ?? 'No Owner' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                    <select name="vendor_filter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        <option value="blank" {{ request('vendor_filter') == 'blank' ? 'selected' : '' }}>Blank</option>
                        <option value="not_blank" {{ request('vendor_filter') == 'not_blank' ? 'selected' : '' }}>Has Vendor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specific Vendor</label>
                    <select name="vendor_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tag Status</label>
                    <select name="tagged" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        <option value="tagged" {{ request('tagged') == 'tagged' ? 'selected' : '' }}>Tagged</option>
                        <option value="untagged" {{ request('tagged') == 'untagged' ? 'selected' : '' }}>Untagged</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Filter
                    </button>
                </div>
            </div>

            <!-- Transaction Type Filters -->
            <div class="border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                <div class="flex gap-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="withdrawal_filter" value="1" {{ request('withdrawal_filter') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Show Withdrawals</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="deposit_filter" value="1" {{ request('deposit_filter') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Show Deposits</span>
                    </label>
                </div>
            </div>

            <!-- Clear Filter Button -->
            <div>
                <a href="{{ route('account-statement.index') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    @if ($statements->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No account statements found.</p>
            <a href="{{ route('account-statement.import.form') }}" class="text-blue-600 hover:underline">
                Import your first statement
            </a>
        </div>
    @else
        <!-- Bulk Tag Section -->
        @if (request('search'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Bulk Tag Results</h3>
                        <p class="text-sm text-blue-700">Found {{ count($statements) }} matching entries. Tag all to a vendor:</p>
                    </div>
                    <div class="flex gap-3 items-center">
                        <select id="bulkVendorSelect" class="px-3 py-2 border border-blue-300 rounded-md text-sm">
                            <option value="">-- Select Vendor --</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="bulkTagVendor()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium">
                            Tag All
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Narration</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Chq/Ref</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Withdrawal</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Deposit</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Flat/Owner</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Vendor</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statements as $statement)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-900">
                                {{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                <div>{{ $statement->narration ?? '-' }}</div>
                                @if ($statement->expense_details)
                                    <div class="text-xs text-gray-600 mt-1"><strong>Details:</strong> {{ Str::limit($statement->expense_details, 80) }}</div>
                                @endif
                                @if ($statement->remark)
                                    <div class="text-xs text-gray-600"><strong>Remark:</strong> {{ Str::limit($statement->remark, 80) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ $statement->chq_ref_no ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right{{ $statement->withdrawal_amt ? ' text-red-600 font-semibold' : '' }}">
                                {{ $statement->withdrawal_amt ? 'Rs.' . number_format($statement->withdrawal_amt, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right{{ $statement->deposit_amt ? ' text-green-600 font-semibold' : '' }}">
                                {{ $statement->deposit_amt ? 'Rs.' . number_format($statement->deposit_amt, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($statement->flat)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                        {{ $statement->flat->flat_number }} - {{ $statement->flat->owner_name ?? 'No Owner' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Not tagged</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($statement->vendor)
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-medium">
                                        {{ $statement->vendor->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Not tagged</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $categories = $statement->categories()->get();
                                @endphp
                                @if ($categories->count() > 0)
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        @foreach($categories as $category)
                                            <span class="text-indigo-600">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Not tagged</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="flex flex-col gap-1">
                                    <button onclick="openTagModal({{ $statement->id }}, '{{ addslashes($statement->narration) }}')" 
                                        class="text-blue-600 hover:text-blue-800 font-medium text-left">
                                        {{ $statement->flat ? 'Change Flat' : 'Tag Flat' }}
                                    </button>
                                    <button onclick="openVendorModal({{ $statement->id }}, '{{ addslashes($statement->narration) }}')" 
                                        class="text-orange-600 hover:text-orange-800 font-medium text-left">
                                        {{ $statement->vendor ? 'Change Vendor' : 'Tag Vendor' }}
                                    </button>
                                    <button onclick="openCategoryModal({{ $statement->id }}, '{{ addslashes($statement->narration) }}')" 
                                        class="text-indigo-600 hover:text-indigo-800 font-medium text-left">
                                        Tag Category
                                    </button>
                                    <a href="{{ route('import-rules.create') }}?match_text={{ urlencode($statement->narration) }}" 
                                        class="text-green-600 hover:text-green-800 font-medium text-left">
                                        Create Rule
                                    </a>
                                    <button onclick="openExpenseModal({{ $statement->id }}, '{{ addslashes($statement->narration) }}')" 
                                        class="text-purple-600 hover:text-purple-800 font-medium text-left">
                                        {{ $statement->withdrawal_amt ? 'Expense Details' : '' }}
                                    </button>
                                    @if ($statement->flat)
                                        <form method="POST" action="{{ route('account-statement.untag-flat', $statement->id) }}" onsubmit="return confirm('Remove flat tag?');">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs text-left">Remove Flat</button>
                                        </form>
                                    @endif
                                    @if ($statement->vendor)
                                        <form method="POST" action="{{ route('account-statement.untag-vendor', $statement->id) }}" onsubmit="return confirm('Remove vendor tag?');">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs text-left">Remove Vendor</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Tag Flat Modal -->
<div id="tagModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-xl h-auto max-h-96 overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Tag Statement to Flat</h2>
        <p id="narrationDisplay" class="text-sm text-gray-600 mb-4"></p>
        
        <form id="tagForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Flat</label>
                <select name="flat_id" id="flatSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="">-- Select a Flat --</option>
                    @foreach ($flats as $flat)
                        <option value="{{ $flat->id }}">
                            {{ $flat->flat_number }} - {{ $flat->owner_name ?? 'No Owner' }} (Rs.{{ number_format($flat->calculated_maintenance, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeTagModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Tag Flat</button>
            </div>
        </form>
    </div>
</div>

<!-- Tag Vendor Modal -->
<div id="vendorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-xl h-auto max-h-96 overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Tag Statement to Vendor</h2>
        <p id="vendorNarrationDisplay" class="text-sm text-gray-600 mb-4"></p>
        
        <!-- Tab Toggle -->
        <div class="flex gap-2 mb-4 border-b">
            <button type="button" onclick="switchVendorMode('select')" id="selectTabBtn" class="pb-2 px-4 border-b-2 border-orange-600 text-orange-600 font-medium">Select Vendor</button>
            <button type="button" onclick="switchVendorMode('create')" id="createTabBtn" class="pb-2 px-4 text-gray-600">Create New</button>
        </div>

        <!-- Select Existing Vendor -->
        <form id="vendorTagForm" method="POST" class="space-y-4" style="display:block;">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Vendor</label>
                <select name="vendor_id" id="vendorSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="">-- Select a Vendor --</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">
                            {{ $vendor->name }} ({{ ucfirst($vendor->vendor_type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeVendorModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Tag Vendor</button>
            </div>
        </form>

        <!-- Create New Vendor -->
        <form id="createVendorTagForm" method="POST" class="space-y-4" style="display:none;">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name *</label>
                <input type="text" name="vendor_name" id="newVendorName" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Enter vendor name" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Type *</label>
                <select name="vendor_type" id="newVendorType" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="">-- Select Type --</option>
                    <option value="service">Service</option>
                    <option value="supply">Supply</option>
                    <option value="contractor">Contractor</option>
                    <option value="cash">Cash Expense</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                <input type="text" name="contact_person" id="newVendorContact" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Optional">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeVendorModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Create & Tag</button>
            </div>
        </form>
    </div>
</div>

<!-- Tag Category Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-xl h-auto max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Add Categories to Statement</h2>
            <button type="button" onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <p id="categoryNarrationDisplay" class="text-sm text-gray-600 mb-4"></p>
        
        <div class="mb-4">
            <input type="text" id="categorySearchInput" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" oninput="filterCategories()">
        </div>
        
        <div id="categoriesContainer" class="space-y-2 border border-gray-300 rounded-lg p-3 bg-gray-50 mb-4 max-h-64 overflow-y-auto">
            <!-- Categories will be loaded here -->
        </div>

        <button type="button" onclick="openAddCategoryForStatement()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mb-4">
            + Add New Category
        </button>
        
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Close</button>
            <button type="button" onclick="saveCategoryTags()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Categories</button>
        </div>
    </div>
</div>

<!-- Add New Category Modal for Statements -->
<div id="addStatementCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold mb-4">Add New Category</h2>
        <p class="text-sm text-gray-600 mb-4">Categories created here will be available for both vendors and account statements.</p>
        <form id="addStatementCategoryForm">
            @csrf
            <input type="hidden" name="type" value="vendor">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <input type="color" name="color" value="#52C966" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
            </div>

            <div id="statementCategoryError" class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 hidden"></div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddCategoryForStatement()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Expense Details Modal -->
<div id="expenseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl h-auto max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Expense Details & Remarks</h2>
            <button type="button" onclick="closeExpenseModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <p id="expenseNarrationDisplay" class="text-sm text-gray-600 mb-4"></p>
        
        <form id="expenseForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expense Details</label>
                <textarea id="expenseDetails" name="expense_details" rows="4" placeholder="Enter details about this expense..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <textarea id="expenseRemark" name="remark" rows="3" placeholder="Enter any remarks or notes..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </textarea>
            </div>
            
            <div id="expenseError" class="p-3 rounded-lg bg-red-100 text-red-700 hidden"></div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeExpenseModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                <button type="button" onclick="saveExpenseDetails()" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentStatementId = null;

function openTagModal(statementId, narration) {
    document.getElementById('tagModal').classList.remove('hidden');
    document.getElementById('narrationDisplay').textContent = 'Narration: ' + narration.substring(0, 50);
    document.getElementById('tagForm').action = '/account-statement/' + statementId + '/tag-flat';
}

function closeTagModal() {
    document.getElementById('tagModal').classList.add('hidden');
}

function openVendorModal(statementId, narration) {
    document.getElementById('vendorModal').classList.remove('hidden');
    document.getElementById('vendorNarrationDisplay').textContent = 'Narration: ' + narration.substring(0, 50);
    document.getElementById('vendorTagForm').action = '/account-statement/' + statementId + '/tag-vendor';
    document.getElementById('createVendorTagForm').action = '/account-statement/' + statementId + '/create-and-tag-vendor';
    currentStatementId = statementId;
}

function closeVendorModal() {
    document.getElementById('vendorModal').classList.add('hidden');
    switchVendorMode('select');
}

function openCategoryModal(statementId, narration) {
    currentStatementId = statementId;
    document.getElementById('categoryModal').classList.remove('hidden');
    document.getElementById('categoryNarrationDisplay').textContent = 'Narration: ' + narration.substring(0, 50);
    
    // Load categories
    loadStatementCategories(statementId);
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
    document.getElementById('categorySearchInput').value = '';
    currentStatementId = null;
}

function openExpenseModal(statementId, narration) {
    currentStatementId = statementId;
    document.getElementById('expenseModal').classList.remove('hidden');
    document.getElementById('expenseNarrationDisplay').textContent = 'Narration: ' + narration.substring(0, 50);
    document.getElementById('expenseForm').action = `/account-statement/${statementId}/update-expense`;
    
    // Load existing expense details
    fetch(`/api/account-statement/${statementId}/expense`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('expenseDetails').value = data.expense_details || '';
            document.getElementById('expenseRemark').value = data.remark || '';
        })
        .catch(error => console.error('Error loading expense details:', error));
}

function closeExpenseModal() {
    document.getElementById('expenseModal').classList.add('hidden');
    currentStatementId = null;
}

function saveExpenseDetails() {
    const expenseDetails = document.getElementById('expenseDetails').value;
    const remark = document.getElementById('expenseRemark').value;
    
    fetch(`/api/account-statement/${currentStatementId}/expense`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            expense_details: expenseDetails,
            remark: remark
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeExpenseModal();
            location.reload();
        } else {
            document.getElementById('expenseError').textContent = data.message || 'Error saving expense details';
            document.getElementById('expenseError').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('expenseError').textContent = 'Error saving expense details';
        document.getElementById('expenseError').classList.remove('hidden');
    });
}

// Close expense modal when clicking outside
document.getElementById('expenseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeExpenseModal();
    }
});

function loadStatementCategories(statementId) {
    // Fetch vendor categories (same as for vendors)
    fetch(`/categories/by-type?type=vendor`)
        .then(response => response.json())
        .then(categories => {
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = '';

            if (categories.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500">No categories available. <a href="{{ route("categories.create") }}" class="text-indigo-600 hover:underline">Create one first.</a></p>';
                return;
            }

            categories.forEach(category => {
                const label = document.createElement('label');
                label.className = 'flex items-center';
                label.innerHTML = `
                    <input type="checkbox" class="category-checkbox" value="${category.id}" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500">
                    <span class="ml-3 text-sm text-gray-700">${category.name}</span>
                    ${category.description ? `<span class="ml-2 text-xs text-gray-500">(${category.description})</span>` : ''}
                `;
                container.appendChild(label);
            });

            // Load current categories for this statement
            fetch(`/api/account-statement/${statementId}/categories`)
                .then(response => response.json())
                .then(data => {
                    const statementCategoryIds = data.categories || [];
                    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                        if (statementCategoryIds.includes(parseInt(checkbox.value))) {
                            checkbox.checked = true;
                        }
                    });
                })
                .catch(error => console.log('Categories loaded for display'));
        })
        .catch(error => console.error('Error loading categories:', error));
}

function filterCategories() {
    const searchTerm = document.getElementById('categorySearchInput').value.toLowerCase();
    const categoryLabels = document.querySelectorAll('#categoriesContainer label');
    
    categoryLabels.forEach(label => {
        const categoryText = label.textContent.toLowerCase();
        if (categoryText.includes(searchTerm)) {
            label.style.display = 'flex';
        } else {
            label.style.display = 'none';
        }
    });
}

function saveCategoryTags() {
    const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
    
    fetch(`/api/account-statement/${currentStatementId}/categories`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ category_ids: selectedCategories })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCategoryModal();
            location.reload(); // Reload to show updated categories
        } else {
            alert('Error saving categories');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving categories');
    });
}

function openAddCategoryForStatement() {
    document.getElementById('addStatementCategoryModal').classList.remove('hidden');
}

function closeAddCategoryForStatement() {
    document.getElementById('addStatementCategoryModal').classList.add('hidden');
    document.getElementById('addStatementCategoryForm').reset();
    document.getElementById('statementCategoryError').classList.add('hidden');
}

document.getElementById('addStatementCategoryForm')?.addEventListener('submit', async function(e) {
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
            document.getElementById('statementCategoryError').textContent = errorMsg;
            document.getElementById('statementCategoryError').classList.remove('hidden');
            return;
        }

        // Reload categories list
        loadStatementCategories(currentStatementId);
        closeAddCategoryForStatement();
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('statementCategoryError').textContent = 'An error occurred. Please try again.';
        document.getElementById('statementCategoryError').classList.remove('hidden');
    }
});

// Close modals when clicking outside
document.getElementById('categoryModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

document.getElementById('addStatementCategoryModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddCategoryForStatement();
    }
});

function switchVendorMode(mode) {
    const selectForm = document.getElementById('vendorTagForm');
    const createForm = document.getElementById('createVendorTagForm');
    const selectTab = document.getElementById('selectTabBtn');
    const createTab = document.getElementById('createTabBtn');

    if (mode === 'select') {
        selectForm.style.display = 'block';
        createForm.style.display = 'none';
        selectTab.className = 'pb-2 px-4 border-b-2 border-orange-600 text-orange-600 font-medium';
        createTab.className = 'pb-2 px-4 text-gray-600';
    } else {
        selectForm.style.display = 'none';
        createForm.style.display = 'block';
        selectTab.className = 'pb-2 px-4 text-gray-600';
        createTab.className = 'pb-2 px-4 border-b-2 border-orange-600 text-orange-600 font-medium';
    }
}

// Close modals when clicking outside
document.getElementById('tagModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTagModal();
    }
});

document.getElementById('vendorModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeVendorModal();
    }
});

// Auto-tag categories when vendor is selected
document.getElementById('vendorSelect')?.addEventListener('change', function() {
    const vendorId = this.value;
    if (!vendorId) return;
    
    // Fetch vendor's categories
    fetch(`/api/vendor/${vendorId}/categories`)
        .then(response => response.json())
        .then(data => {
            if (data.categories && data.categories.length > 0) {
                // Auto-tag these categories to the statement
                const categoryIds = data.categories.map(cat => cat.id);
                autoTagStatementCategories(categoryIds);
            }
        })
        .catch(error => console.error('Error loading vendor categories:', error));
});

function autoTagStatementCategories(categoryIds) {
    if (!currentStatementId || categoryIds.length === 0) return;
    
    fetch(`/api/account-statement/${currentStatementId}/categories`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ category_ids: categoryIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Categories auto-tagged successfully');
        }
    })
    .catch(error => console.error('Error auto-tagging categories:', error));
}

// Bulk tag function
function bulkTagVendor() {
    const vendorId = document.getElementById('bulkVendorSelect').value;
    const search = new URLSearchParams(window.location.search).get('search');

    if (!vendorId) {
        alert('Please select a vendor');
        return;
    }

    if (!search) {
        alert('Please search first before bulk tagging');
        return;
    }

    if (confirm('Tag all searched entries to this vendor?')) {
        // Submit a form to the bulk tag endpoint
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/account-statement/bulk-tag-vendor`;
        
        const vendorField = document.createElement('input');
        vendorField.type = 'hidden';
        vendorField.name = 'vendor_id';
        vendorField.value = vendorId;
        
        const searchField = document.createElement('input');
        searchField.type = 'hidden';
        searchField.name = 'search';
        searchField.value = search;
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(vendorField);
        form.appendChild(searchField);
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
