@extends('layouts.app')

@section('title', 'Petty Cash Expense Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Petty Cash Expense Report</h1>
            <p class="text-gray-600">Track and manage petty cash expenses</p>
        </div>
        <a href="{{ route('petty-cash.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
            + Add Entry
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Total Withdrawals -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg shadow-lg p-6 border-t-4 border-indigo-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Total Withdrawals</p>
                    <p class="text-3xl font-bold mt-2">Rs.{{ number_format($totalWithdrawals, 2) }}</p>
                </div>
                <div class="text-indigo-300 opacity-50">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Expense Paid -->
        <div class="bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-lg shadow-lg p-6 border-t-4 border-rose-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-100 text-sm font-medium">Total Expenses Paid</p>
                    <p class="text-3xl font-bold mt-2">Rs.{{ number_format($totalExpensePaid, 2) }}</p>
                </div>
                <div class="text-rose-300 opacity-50">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm1-13h-2v6h2zm0 8h-2v2h2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Maintenance Total -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-lg shadow-lg p-6 border-t-4 border-amber-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Maintenance</p>
                    <p class="text-3xl font-bold mt-2">Rs.{{ number_format($categoryTotals->get('Maintenance', 0), 2) }}</p>
                </div>
                <div class="text-amber-300 opacity-50">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Miscellaneous Total -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-lg shadow-lg p-6 border-t-4 border-emerald-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Miscellaneous</p>
                    <p class="text-3xl font-bold mt-2">Rs.{{ number_format($categoryTotals->get('Miscellaneous', 0), 2) }}</p>
                </div>
                <div class="text-emerald-300 opacity-50">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2zm0-4h-2V7h2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('petty-cash.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="Maintenance" {{ request('category') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Miscellaneous" {{ request('category') === 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    Filter
                </button>
                <a href="{{ route('petty-cash.index') }}" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition font-medium text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Glowing Table -->
    <div class="bg-white rounded-lg shadow-2xl overflow-hidden border border-gray-200">
        <!-- Table Header with Glow Effect -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white p-6 border-b-4 border-blue-700 shadow-lg">
            <h2 class="text-2xl font-bold">Petty Cash Transactions</h2>
            <p class="text-blue-100 text-sm mt-1">{{ $pettyDetails->count() }} entries</p>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-300">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Sr. No.</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Petty Cash Amt</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Availability</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Expense Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Expense Paid</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Payment By</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Vendor Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Expense Details</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Remark</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pettyDetails as $key => $detail)
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition duration-200 {{ $key % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-4 py-3">
                                {{ $key + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $detail->date?->format('d-m-Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($detail->is_withdrawal)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        Rs.{{ number_format($detail->amount, 2) }} (Withdraw)
                                    </span>
                                @else
                                    <span class="text-gray-700">{{ $detail->amount ? 'Rs.' . number_format($detail->amount, 2) : 'NA' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($detail->availability)
                                 {{ $detail->availability }}

                                @else
                                    <span class="text-gray-400">NA</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($detail->expense_type)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        {{ $detail->expense_type }}
                                    </span>
                                @else
                                    <span class="text-gray-400">NA</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-red-600">
                                Rs.{{ number_format($detail->expense_paid, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                    {{ $detail->payment_method }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $detail->vendor_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                @if($detail->expense_details)
                                    {{ Str::limit($detail->expense_details, 50) }}  
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                @if($detail->remark)
                                    {{ Str::limit($detail->remark, 50) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('petty-cash.edit', $detail) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                        Edit
                                    </a>
                                    <form action="{{ route('petty-cash.destroy', $detail) }}" method="POST" style="display: inline;" 
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <p class="text-lg font-medium">No petty cash entries found</p>
                                <a href="{{ route('petty-cash.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                    Create your first entry
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-t-2 border-gray-300 px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded p-3 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-xs font-medium">Total Entries</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $pettyDetails->count() }}</p>
                </div>
                <div class="bg-white rounded p-3 border-l-4 border-rose-500">
                    <p class="text-gray-600 text-xs font-medium">Total Expenses</p>
                    <p class="text-2xl font-bold text-rose-600">Rs.{{ number_format($totalExpensePaid, 2) }}</p>
                </div>
                <div class="bg-white rounded p-3 border-l-4 border-indigo-500">
                    <p class="text-gray-600 text-xs font-medium">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-indigo-600">Rs.{{ number_format($totalWithdrawals, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Glowing effect for table header */
    .bg-gradient-to-r.from-blue-600 {
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3), 
                    0 0 20px rgba(59, 130, 246, 0.2);
    }

    /* Row hover glow */
    tbody tr:hover {
        box-shadow: inset 0 0 10px rgba(59, 130, 246, 0.1);
    }

    /* Table container glow */
    .shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25),
                    0 0 30px rgba(59, 130, 246, 0.15);
    }
</style>
@endsection
