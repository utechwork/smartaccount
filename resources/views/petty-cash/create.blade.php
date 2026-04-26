@extends('layouts.app')

@section('title', 'Create Petty Cash Entry')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Petty Cash Entry</h1>
        <a href="{{ route('petty-cash.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Report
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form action="{{ route('petty-cash.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('date') }}">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Petty Cash Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Petty Cash Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('amount') }}" placeholder="0.00">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability -->
                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                    <select id="availability" name="availability" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select...</option>
                        <option value="Yes" {{ old('availability') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('availability') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('availability')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Type -->
                <div>
                    <label for="expense_type" class="block text-sm font-medium text-gray-700 mb-2">Expense Type</label>
                    <input type="text" id="expense_type" name="expense_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('expense_type') }}" placeholder="e.g., Maintenance">
                    @error('expense_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Paid -->
                <div>
                    <label for="expense_paid" class="block text-sm font-medium text-gray-700 mb-2">Expense Paid <span class="text-red-500">*</span></label>
                    <input type="number" id="expense_paid" name="expense_paid" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('expense_paid', 0) }}" placeholder="0.00">
                    @error('expense_paid')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                    <select id="payment_method" name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Card" {{ old('payment_method') === 'Card' ? 'selected' : '' }}>Card</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cleared By -->
                <div>
                    <label for="cleared_by" class="block text-sm font-medium text-gray-700 mb-2">Cleared By</label>
                    <input type="text" id="cleared_by" name="cleared_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('cleared_by') }}" placeholder="e.g., HDFC Bank Ltd">
                    @error('cleared_by')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor Name -->
                <div>
                    <label for="vendor_name" class="block text-sm font-medium text-gray-700 mb-2">Vendor Name</label>
                    <input type="text" id="vendor_name" name="vendor_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('vendor_name') }}" placeholder="Vendor name">
                    @error('vendor_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Category -->
                <div>
                    <label for="expense_category" class="block text-sm font-medium text-gray-700 mb-2">Expense Category</label>
                    <select id="expense_category" name="expense_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Category</option>
                        <option value="Maintenance" {{ old('expense_category') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Miscellaneous" {{ old('expense_category') === 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                    </select>
                    @error('expense_category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Expense Details -->
            <div>
                <label for="expense_details" class="block text-sm font-medium text-gray-700 mb-2">Expense Details</label>
                <textarea id="expense_details" name="expense_details" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter detailed information about the expense">{{ old('expense_details') }}</textarea>
                @error('expense_details')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remark -->
            <div>
                <label for="remark" class="block text-sm font-medium text-gray-700 mb-2">Remark</label>
                <textarea id="remark" name="remark" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Additional remarks">{{ old('remark') }}</textarea>
                @error('remark')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Withdrawal -->
            <div class="flex items-center">
                <input type="checkbox" id="is_withdrawal" name="is_withdrawal" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_withdrawal') ? 'checked' : '' }}>
                <label for="is_withdrawal" class="ml-2 text-sm text-gray-700">Mark as Withdrawal from Bank</label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    Save Entry
                </button>
                <a href="{{ route('petty-cash.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
