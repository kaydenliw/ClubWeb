@extends('layouts.dashboard')

@section('page-title', 'Edit Organization')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Organizations', 'url' => route('admin.organizations.index')],
    ['label' => 'Edit', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Organization</h1>
            <p class="text-sm text-gray-500 mt-1">Update organization details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.organizations.show', $organization) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                View Details
            </a>
            <a href="{{ route('admin.organizations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.organizations.update', $organization) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm border border-gray-100">
        @csrf
        @method('PUT')
        
        <!-- Organization Information Section -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Organization Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Organization Name *</label>
                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $organization->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ $organization->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $organization->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Address</label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $organization->address) }}</textarea>
                @error('address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>


        <!-- Bank Details Section -->
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Bank Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $organization->bank_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_name') border-red-500 @enderror">
                    @error('bank_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $organization->bank_account_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_number') border-red-500 @enderror">
                    @error('bank_account_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Account Holder</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $organization->bank_account_holder) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_holder') border-red-500 @enderror">
                    @error('bank_account_holder')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.organizations.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Update Organization
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
