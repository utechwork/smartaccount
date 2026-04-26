@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Import Account Statement</h1>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('account-statement.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Select CSV File
                </label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p class="text-xs text-gray-500 mt-1">
                    Expected columns: Date, Narration, Chq/Ref.No., Value Date (DD/MM/YY), Withdrawal Amt., Deposit Amt., Closing Balance
                </p>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                Import CSV
            </button>
        </form>
    </div>
</div>
@endsection
