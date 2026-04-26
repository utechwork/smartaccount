@extends('layouts.app')

@section('title', $vendor->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('vendors.index') }}" class="text-blue-600 hover:underline">&larr; Back to Vendors</a>
        <a href="{{ route('vendors.export.pdf', $vendor) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
            📄 Export PDF
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-blue-600">{{ $vendor->name }}</div>
            <div class="text-gray-600 text-sm mt-1">
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
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-4xl font-bold text-green-600">Rs.{{ number_format($totalExpenses, 2) }}</div>
            <div class="text-gray-600 mt-2">Total Expenses</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-4xl font-bold text-gray-900">{{ $statements->count() }}</div>
            <div class="text-gray-600 mt-2">Transactions</div>
        </div>
    </div>

    <!-- Vendor Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Vendor Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Contact Person</label>
                <p class="text-gray-900">{{ $vendor->contact_person ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Phone</label>
                <p class="text-gray-900">{{ $vendor->phone ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Email</label>
                <p class="text-gray-900">{{ $vendor->email ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Address</label>
                <p class="text-gray-900">{{ $vendor->address ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Categories</label>
                @php
                    $categories = $vendor->categories()->get();
                @endphp
                @if($categories->count())
                    <div class="flex flex-wrap gap-2 mt-1 text-sm">
                        @foreach($categories as $category)
                            <span class="text-indigo-600">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm">—</p>
                @endif
            </div>
        </div>
        @if ($vendor->description)
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-600">Description</label>
                <p class="text-gray-900">{{ $vendor->description }}</p>
            </div>
        @endif
        <div class="mt-6 flex gap-3">
            <a href="{{ route('vendors.edit', $vendor) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Edit Vendor
            </a>
            <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Delete</button>
            </form>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Narration</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Chq/Ref</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-700">Withdrawal</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-700">Deposit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($statements as $statement)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-900">
                            {{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-3 text-gray-900">
                            <div title="{{ $statement->narration ?? '' }}">
                                {{ $statement->narration ?? '-' }}
                            </div>
                            @if ($statement->expense_details)
                                <div class="text-xs text-gray-600 mt-1"><strong>Details:</strong> {{ Str::limit($statement->expense_details, 80) }}</div>
                            @endif
                            @if ($statement->remark)
                                <div class="text-xs text-gray-600"><strong>Remark:</strong> {{ Str::limit($statement->remark, 80) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-900">
                            {{ $statement->chq_ref_no ?? '-' }}
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-red-600">
                            {{ $statement->withdrawal_amt ? 'Rs.' . number_format($statement->withdrawal_amt, 2) : '-' }}
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-green-600">
                            {{ $statement->deposit_amt ? 'Rs.' . number_format($statement->deposit_amt, 2) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No transactions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
