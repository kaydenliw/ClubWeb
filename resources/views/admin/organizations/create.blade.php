@extends('layouts.dashboard')

@section('page-title', 'Add Organization')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Organizations', 'url' => route('admin.organizations.index')],
    ['label' => 'Create', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Organization</h1>
            <p class="text-sm text-gray-500 mt-1">Create a new car club organization</p>
        </div>
        <a href="{{ route('admin.organizations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.organizations.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm border border-gray-100">
        @csrf

        <!-- Organization Information Section -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Organization Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Organization Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Organization Type *</label>
                    <select name="organization_type_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('organization_type_id') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        @foreach($organizationTypes as $type)
                        <option value="{{ $type->id }}" {{ old('organization_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->icon }} {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('organization_type_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">PIC Name</label>
                    <input type="text" name="pic_name" value="{{ old('pic_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pic_name') border-red-500 @enderror">
                    @error('pic_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Address</label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                @error('address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Logo</label>
                <div class="relative">
                    <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="updateLogoFileName(this)">
                    <label for="logo" class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition @error('logo') border-red-500 @enderror">
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500" id="logo-file-name">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </label>
                </div>
                @error('logo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_name') border-red-500 @enderror">
                    @error('bank_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_number') border-red-500 @enderror">
                    @error('bank_account_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Account Holder</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account_holder') border-red-500 @enderror">
                    @error('bank_account_holder')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Platform Fee Configuration Section -->
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Platform Fee Configuration</h3>
            <p class="text-xs text-gray-500 mt-1">Set default platform fee for all charges in this organization</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Percentage (%)</label>
                    <input type="number" name="platform_fee_percentage" value="{{ old('platform_fee_percentage') }}" step="0.01" min="0" max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('platform_fee_percentage') border-red-500 @enderror">
                    @error('platform_fee_percentage')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Operator</label>
                    <select name="platform_fee_operator" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('platform_fee_operator') border-red-500 @enderror">
                        <option value="">-</option>
                        <option value="and">AND</option>
                        <option value="or">OR</option>
                    </select>
                    @error('platform_fee_operator')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Fixed Amount (RM)</label>
                    <input type="number" name="platform_fee_fixed" value="{{ old('platform_fee_fixed') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('platform_fee_fixed') border-red-500 @enderror">
                    @error('platform_fee_fixed')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Example: 5% AND RM5 means charge 5% + RM5. Leave percentage empty for flat RM5 fee.</p>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.organizations.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Create Organization
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateLogoFileName(input) {
    const fileNameDisplay = document.getElementById('logo-file-name');
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('font-medium', 'text-green-600');
    } else {
        fileNameDisplay.textContent = 'PNG, JPG, GIF up to 2MB';
        fileNameDisplay.classList.remove('font-medium', 'text-green-600');
    }
}
</script>
@endpush

@endsection
