@extends('layouts.dashboard')

@section('page-title', 'Add Settlement')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Settlements', 'url' => route('admin.settlements.index')],
    ['label' => 'Create', 'url' => null]
]])
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Add New Settlement</h3>
            <p class="text-sm text-gray-500 mt-1">Create a new settlement for an organization</p>
        </div>
        <a href="{{ route('admin.settlements.index') }}" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.settlements.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Organization</label>
                    <select name="organization_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (RM)</label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Settlement Date</label>
                    <input type="date" name="settlement_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.settlements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Create Settlement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
