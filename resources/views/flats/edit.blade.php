@extends('layouts.app')

@section('title', 'Edit Flat - ' . $flat->flat_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Edit Flat {{ $flat->flat_number }}</h1>
            <a href="{{ route('flats.export.pdf', $flat) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
                📄 Export PDF
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('flats.update', $flat) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Flat Number</label>
                    <input type="text" value="{{ $flat->flat_number }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Floor Number</label>
                    <input type="text" value="Floor {{ $flat->floor_number }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                </div>

                <div class="mb-4">
                    <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                    <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name', $flat->owner_name) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('owner_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="owner_email" class="block text-sm font-medium text-gray-700 mb-2">Owner Email</label>
                    <input type="email" id="owner_email" name="owner_email" value="{{ old('owner_email', $flat->owner_email) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('owner_email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="owner_phone" class="block text-sm font-medium text-gray-700 mb-2">Owner Phone</label>
                    <input type="tel" id="owner_phone" name="owner_phone" value="{{ old('owner_phone', $flat->owner_phone) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('owner_phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Flat Type</label>
                    <select name="flat_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1BHK" {{ old('flat_type', $flat->flat_type) == '1BHK' ? 'selected' : '' }}>1 BHK</option>
                        <option value="2BHK" {{ old('flat_type', $flat->flat_type) == '2BHK' ? 'selected' : '' }}>2 BHK</option>
                    </select>
                    @error('flat_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="occupancy_type" class="block text-sm font-medium text-gray-700 mb-2">Occupancy Type</label>
                    <select id="occupancy_type" name="occupancy_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="owner" {{ old('occupancy_type', $flat->occupancy_type) == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="tenant" {{ old('occupancy_type', $flat->occupancy_type) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                    </select>
                    @error('occupancy_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Maintenance Amount</label>
                    <input type="text" value="Rs.{{ number_format($flat->calculated_maintenance, 2) }}" disabled 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">This amount is automatically calculated based on flat type and occupancy</p>
                </div>

                <div class="mb-4">
                    <label for="maintenance_status" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Status</label>
                    <select id="maintenance_status" name="maintenance_status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ old('maintenance_status', $flat->maintenance_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('maintenance_status', $flat->maintenance_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ old('maintenance_status', $flat->maintenance_status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                    @error('maintenance_status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">Last Maintenance Date</label>
                    <input type="date" id="last_maintenance_date" name="last_maintenance_date" value="{{ old('last_maintenance_date', $flat->last_maintenance_date?->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('last_maintenance_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $flat->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="builder_paid_exception" name="builder_paid_exception" value="1" 
                            {{ old('builder_paid_exception', $flat->builder_paid_exception) ? 'checked' : '' }}
                            class="w-4 h-4 border border-gray-300 rounded">
                        <span class="ml-2 text-sm font-medium text-gray-700">
                            Builder Paid Exception (Maintenance already paid to builder)
                        </span>
                    </label>
                    @error('builder_paid_exception')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                        Update Flat
                    </button>
                    <a href="{{ route('flats.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
