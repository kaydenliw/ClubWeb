@extends('layouts.dashboard')

@section('title', 'Edit Charge')
@section('page-title', 'Edit Charge')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Charges', 'url' => route('organization.charges.index')],
    ['label' => 'Edit', 'url' => null]
]])
<div class="max-w-3xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Charge</h1>
            <p class="text-sm text-gray-500 mt-1">Update charge details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organization.charges.show', $charge) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                View Details
            </a>
            <a href="{{ route('organization.charges.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('organization.charges.update', $charge) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Charge Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $charge->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $charge->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Amount (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           value="{{ old('amount', $charge->amount) }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type"
                            id="type"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="monthly" {{ old('type', $charge->type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('type', $charge->type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="one-time" {{ old('type', $charge->type) == 'one-time' ? 'selected' : '' }}>One-time</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $charge->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $charge->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($charge->image_path)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Image</label>
                    <img src="{{ asset('storage/' . $charge->image_path) }}"
                         alt="{{ $charge->name }}"
                         class="w-32 h-32 rounded-lg object-cover border border-gray-200">
                </div>
                @endif

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $charge->image_path ? 'Replace Image' : 'Image' }}
                    </label>
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.charges.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Update Charge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
