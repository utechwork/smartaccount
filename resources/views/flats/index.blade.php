@extends('layouts.app')

@section('title', 'Flats Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-4xl font-bold">Flats Management</h1>
            <a href="{{ route('flats.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Add New Flat
            </a>
        </div>

        <!-- Statistics Dashboard -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-4xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-gray-600 mt-2">Total Flats</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-4xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                <div class="text-gray-600 mt-2">Pending Payment</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-4xl font-bold text-green-600">{{ $stats['paid'] }}</div>
                <div class="text-gray-600 mt-2">Paid</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-4xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                <div class="text-gray-600 mt-2">Overdue</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('flats.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                <select name="floor" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Floors</option>
                    @for ($i = 1; $i <= 11; $i++)
                        <option value="{{ $i }}" {{ request('floor') == $i ? 'selected' : '' }}>
                            Floor {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="flat_number" {{ request('sort') == 'flat_number' ? 'selected' : '' }}>Flat Number</option>
                    <option value="floor_number" {{ request('sort') == 'floor_number' ? 'selected' : '' }}>Floor</option>
                    <option value="owner_name" {{ request('sort') == 'owner_name' ? 'selected' : '' }}>Owner Name</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Flats Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Flat Number</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Floor</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Owner Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Occupancy</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Maintenance</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($flats as $flat)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $flat->flat_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">Floor {{ $flat->floor_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $flat->flat_type === '1BHK' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $flat->flat_type }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $flat->owner_name ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $flat->occupancy_type === 'owner' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($flat->occupancy_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">
                            @if ($flat->builder_paid_exception)
                                <span class="text-red-600 font-semibold">Builder Paid</span>
                            @else
                                Rs.{{ number_format($flat->calculated_maintenance, 2) }}
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if ($flat->maintenance_status === 'paid')
                                    bg-green-100 text-green-800
                                @elseif ($flat->maintenance_status === 'overdue')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                {{ ucfirst($flat->maintenance_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center text-sm">
                            <a href="{{ route('flats.edit', $flat) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('flats.destroy', $flat) }}" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-3">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No flats found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
