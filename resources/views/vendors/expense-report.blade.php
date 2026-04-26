@extends('layouts.app')

@section('title', 'Vendor Expense Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-4xl font-bold">Vendor Expense Report</h1>
        <div class="flex gap-3">
            <a href="{{ route('vendors.expense-report.export.pdf') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
                📄 Export PDF
            </a>
            <a href="{{ route('vendors.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Back to Vendors
            </a>
        </div>
    </div>

    <!-- Overall Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Vendors</p>
                <p class="text-4xl font-bold">{{ $vendors->count() }}</p>
            </div>
            <div>
                <p class="text-blue-100 text-sm font-medium">Overall Total Expense</p>
                <p class="text-4xl font-bold">Rs.{{ number_format($overallTotal, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Vendors List -->
    @if($vendors->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No vendors found.</p>
            <a href="{{ route('vendors.create') }}" class="text-blue-600 hover:underline">
                Create your first vendor
            </a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($vendors as $vendor)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <div class="p-6">
                        <!-- Vendor Header -->
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h2 class="text-lg font-bold text-gray-900 mb-1">
                                    <a href="{{ route('vendors.show', $vendor) }}" class="text-blue-600 hover:underline">
                                        {{ $vendor->name }}
                                    </a>
                                </h2>
                                @php
                                    $categories = $vendor->categories()->get();
                                @endphp
                                @if($categories->count() > 0)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @foreach($categories as $category)
                                            <span class="text-xs text-indigo-600">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="text-right ml-auto">
                                <p class="text-lg font-bold text-green-600 mb-0">Rs.{{ number_format($vendor->total_expenses, 2) }}</p>
                                @php
                                    $percentage = $overallTotal > 0 ? ($vendor->total_expenses / $overallTotal) * 100 : 0;
                                @endphp
                                <p class="text-xs text-gray-500 m-0">{{ number_format($percentage, 1) }}% of total</p>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4 mb-4">
                            @if($vendor->contact_person)
                                <div>
                                    <p class="text-gray-500">Contact Person</p>
                                    <p class="text-gray-900 font-medium">{{ $vendor->contact_person }}</p>
                                </div>
                            @endif
                            @if($vendor->phone)
                                <div>
                                    <p class="text-gray-500">Phone</p>
                                    <p class="text-gray-900 font-medium">{{ $vendor->phone }}</p>
                                </div>
                            @endif
                            @if($vendor->email)
                                <div>
                                    <p class="text-gray-500">Email</p>
                                    <p class="text-gray-900 font-medium text-blue-600">{{ $vendor->email }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Transactions Table -->
                        @php
                            $vendorStatements = $vendor->statements()
                                                       ->whereNotNull('withdrawal_amt')
                                                       ->orderBy('date', 'desc')
                                                       ->get();
                        @endphp
                        @if($vendorStatements->count() > 0)
                            <div class="pt-4 border-t">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Transactions ({{ $vendorStatements->count() }})</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="px-3 py-2 text-left text-gray-600">Date</th>
                                                <th class="px-3 py-2 text-left text-gray-600">Narration / Details / Remark</th>
                                                <th class="px-3 py-2 text-left text-gray-600">Chq/Ref</th>
                                                <th class="px-3 py-2 text-right text-gray-600">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendorStatements as $statement)
                                                <tr class="border-b hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-gray-700">
                                                        {{ $statement->date ? $statement->date->format('d/m/Y') : '-' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-gray-700">
                                                        <div class="font-medium text-gray-900 mb-1">{{ $statement->narration ?? '-' }}</div>
                                                        @if ($statement->expense_details)
                                                            <div class="text-xs text-gray-600 mt-1 p-1 bg-gray-50 rounded">
                                                                <span class="font-semibold">Details:</span> {{ $statement->expense_details }}
                                                            </div>
                                                        @endif
                                                        @if ($statement->remark)
                                                            <div class="text-xs text-gray-600 mt-1 p-1 bg-blue-50 rounded">
                                                                <span class="font-semibold">Remark:</span> {{ $statement->remark }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-gray-700">
                                                        {{ $statement->chq_ref_no ?? '-' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right font-medium text-red-600 whitespace-nowrap">
                                                        Rs.{{ number_format($statement->withdrawal_amt, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Action -->
                        <div class="mt-4 flex gap-3">
                            <a href="{{ route('vendors.show', $vendor) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Details →</a>
                            <a href="{{ route('vendors.edit', $vendor) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="h-1 bg-gray-100">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600" 
                             style="width: {{ $percentage }}%">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style media="print">
    button { display: none !important; }
    .bg-gradient-to-r { break-inside: avoid; }
</style>
@endsection
