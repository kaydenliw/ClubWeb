@extends('layouts.dashboard')

@section('title', 'Edit Bank Details')
@section('page-title', 'Edit Bank Details')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('organization.settlements.update-bank') }}">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Bank Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="bank_name"
                           id="bank_name"
                           value="{{ old('bank_name', $organization->bank_name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_name') border-red-500 @enderror">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Account Name -->
                <div>
                    <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Account Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="bank_account_name"
                           id="bank_account_name"
                           value="{{ old('bank_account_name', $organization->bank_account_name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_name') border-red-500 @enderror">
                    @error('bank_account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Account Number -->
                <div>
                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Account Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="bank_account_number"
                           id="bank_account_number"
                           value="{{ old('bank_account_number', $organization->bank_account_number) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_number') border-red-500 @enderror">
                    @error('bank_account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.settlements.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Update Bank Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
