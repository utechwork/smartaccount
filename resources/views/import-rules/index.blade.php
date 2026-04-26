@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Import Rules</h1>
        <div class="space-x-2">
            <a href="{{ route('import-rules.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Rule
            </a>
            <button type="button" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" data-bs-toggle="modal" data-bs-target="#applyRulesModal">
                Apply to Old Data
            </button>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($rules->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p>No import rules found. <a href="{{ route('import-rules.create') }}" class="underline font-bold">Create one now</a></p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Priority</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Match Text</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Vendor</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Categories</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $rule->priority }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $rule->name }}</td>
                            <td class="border border-gray-300 px-4 py-2 font-mono text-sm">{{ $rule->match_text }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $rule->vendor->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($rule->categories->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($rule->categories as $category)
                                            <span class="inline-block bg-blue-200 text-blue-800 text-xs px-2 py-1 rounded">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 italic">None</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($rule->active)
                                    <span class="inline-block bg-green-200 text-green-800 text-xs px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">Inactive</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="{{ route('import-rules.edit', $rule->id) }}" class="text-blue-600 hover:text-blue-800 font-bold mr-3">Edit</a>
                                <form method="POST" action="{{ route('import-rules.destroy', $rule->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 px-4 py-2 text-center text-gray-500">No rules found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Apply Rules Modal -->
<div class="modal fade" id="applyRulesModal" tabindex="-1" role="dialog" aria-labelledby="applyRulesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('import-rules.apply') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="applyRulesModalLabel">Apply Import Rules to Old Data</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Warning:</strong> This will apply the import rules to existing account statements.
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="overwrite" id="overwrite" value="1">
                        <label class="form-check-label" for="overwrite">
                            <strong>Overwrite existing vendor/category assignments</strong>
                            <br>
                            <small class="text-muted">If unchecked, rules will only be applied to statements without a vendor.</small>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Rules</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
