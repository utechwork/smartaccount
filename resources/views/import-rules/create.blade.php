@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">Create Import Rule</h1>
        <a href="{{ route('import-rules.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Rules</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('import-rules.store') }}" method="POST">
             @csrf
            @include('import-rules.form', ['importRule' => null, 'selectedCategories' => []])

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Create Rule
                </button>
                <a href="{{ route('import-rules.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
