@extends('layouts.dashboard')

@section('page-title', 'Edit Member')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Members', 'url' => route('organization.members.index')],
    ['label' => 'Edit Member', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Member</h1>
            <p class="text-sm text-gray-500 mt-1">Update member details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organization.members.show', $member) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                View Details
            </a>
            <a href="{{ route('organization.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('organization.members.update', $member) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100">
        @csrf
        @method('PUT')

        <!-- Member Information Section -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Member Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ $member->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $member->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Address</label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $member->address) }}</textarea>
                @error('address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Car Details Section -->
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Car Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Brand</label>
                    <input type="text" name="car_brand" value="{{ old('car_brand', $member->car_brand) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('car_brand') border-red-500 @enderror">
                    @error('car_brand')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Model</label>
                    <input type="text" name="car_model" value="{{ old('car_model', $member->car_model) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('car_model') border-red-500 @enderror">
                    @error('car_model')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Plate</label>
                    <input type="text" name="car_plate" value="{{ old('car_plate', $member->car_plate) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('car_plate') border-red-500 @enderror">
                    @error('car_plate')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-lg">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('organization.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Update Member
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
