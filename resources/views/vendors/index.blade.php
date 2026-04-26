@extends('layouts.app')

@section('title', 'Vendors Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-4xl font-bold">Vendors Management</h1>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                🖨 Print
            </button>
            <a href="{{ route('vendors.expense-report') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Expense Report
            </a>
            <a href="{{ route('vendors.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Add New Vendor
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('vendors.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or contact..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Types</option>
                    <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="supply" {{ request('type') == 'supply' ? 'selected' : '' }}>Supply</option>
                    <option value="contractor" {{ request('type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
                    <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Cash Expense</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    @if ($vendors->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No vendors found.</p>
            <a href="{{ route('vendors.create') }}" class="text-blue-600 hover:underline">
                Create your first vendor
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Vendor Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Contact Person</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Paid</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $vendor)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                <a href="{{ route('vendors.show', $vendor) }}" class="text-blue-600 hover:underline">
                                    {{ $vendor->name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($vendor->vendor_type === 'service')
                                        bg-blue-100 text-blue-800
                                    @elseif($vendor->vendor_type === 'supply')
                                        bg-green-100 text-green-800
                                    @elseif($vendor->vendor_type === 'contractor')
                                        bg-purple-100 text-purple-800
                                    @elseif($vendor->vendor_type === 'cash')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst($vendor->vendor_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm">
                                @php
                                    $vendorCategories = $vendor->categories()->get();
                                @endphp
                                @if($vendorCategories->count())
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        @foreach($vendorCategories as $category)
                                            <span class="text-indigo-600">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $vendor->contact_person ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $vendor->phone ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $vendor->email ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-right font-semibold text-blue-600">
                                Rs.{{ number_format($vendor->statements()->whereNotNull('withdrawal_amt')->sum('withdrawal_amt'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Table -->
        <div class="bg-white rounded-lg shadow mt-8 overflow-hidden">
            <div class="p-6 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Summary Table</h3>
            </div>
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Vendor Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Expense</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = $vendors->sum(function($vendor) {
                            return $vendor->statements()->whereNotNull('withdrawal_amt')->sum('withdrawal_amt');
                        });
                    @endphp
                    @foreach($vendors as $vendor)
                        @php
                            $vendorTotal = $vendor->statements()->whereNotNull('withdrawal_amt')->sum('withdrawal_amt');
                            $percentage = $grandTotal > 0 ? ($vendorTotal / $grandTotal) * 100 : 0;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-medium text-blue-600">
                                <a href="{{ route('vendors.show', $vendor) }}" class="hover:underline">
                                    {{ $vendor->name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-green-600">
                                Rs.{{ number_format($vendorTotal, 2) }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right font-medium">
                                {{ number_format($percentage, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 border-t-2 border-gray-300">
                        <td class="px-6 py-3 text-sm font-bold text-gray-900">TOTAL</td>
                        <td class="px-6 py-3 text-sm font-bold text-gray-900">Rs.{{ number_format($grandTotal, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-right font-bold text-gray-900">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>

<style media="print">
    button, [onclick*="print"] { display: none !important; }
</style>
@endsection
