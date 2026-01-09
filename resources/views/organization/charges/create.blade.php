@extends('layouts.dashboard')

@section('title', 'Create Charge or Plan')
@section('page-title', 'Create New Charge or Plan')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Charges & Plans', 'url' => route('organization.charges.index')],
    ['label' => 'Create', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <!-- Info Banner -->
    <div class="mb-4 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm">
                <p class="font-medium text-gray-900 mb-1">What are you creating?</p>
                <ul class="space-y-1 text-gray-700">
                    <li><span class="font-semibold text-blue-600">Subscription Plan</span> - Recurring payments (e.g., monthly membership fees, annual dues)</li>
                    <li><span class="font-semibold text-purple-600">One-Time Charge</span> - Single payments (e.g., event tickets, workshop fees, merchandise)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('organization.charges.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title') }}"
                           placeholder="e.g., Gold Membership, Annual Conference Ticket"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')
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
                              rows="5"
                              class="summernote">{{ old('description') }}</textarea>
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
                           value="{{ old('amount') }}"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recurring -->
                <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-start p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="is_recurring" value="1"
                                   {{ old('is_recurring') == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mt-0.5">
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Subscription Plan (Recurring)</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Members pay regularly (monthly/yearly). Perfect for membership fees.</p>
                            </div>
                        </label>
                        <label class="flex items-start p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="is_recurring" value="0"
                                   {{ old('is_recurring', '0') == '0' ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500 mt-0.5">
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">One-Time Charge</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Single payment only. Perfect for events, workshops, or merchandise.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Recurring Months (shown when recurring is selected) -->
                <div id="recurringMonthsField" style="display: none;">
                    <label for="recurring_months" class="block text-sm font-medium text-gray-700 mb-1">
                        Billing Frequency <span class="text-red-500">*</span>
                    </label>
                    <select name="recurring_months" id="recurring_months"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select frequency</option>
                        <option value="1" {{ old('recurring_months') == 1 ? 'selected' : '' }}>Monthly</option>
                        <option value="3" {{ old('recurring_months') == 3 ? 'selected' : '' }}>3 Months</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">How often members will be charged for this plan</p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Scheduled Date -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Scheduled Date & Time
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                           value="{{ old('scheduled_at') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Optional: Schedule when this charge becomes active</p>
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Image
                    </label>
                    <div class="relative">
                        <input type="file"
                               name="image"
                               id="image"
                               accept="image/*"
                               class="hidden"
                               onchange="updateImageFileName(this)">
                        <label for="image" class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition @error('image') border-red-500 @enderror">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500" id="image-file-name">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </label>
                    </div>
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
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
// Update image filename display
function updateImageFileName(input) {
    const fileNameDisplay = document.getElementById('image-file-name');
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

$(document).ready(function() {
    // Initialize Summernote
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']]
        ]
    });

    // Toggle recurring months field
    function toggleRecurringMonths() {
        const isRecurring = $('input[name="is_recurring"]:checked').val();
        if (isRecurring === '1') {
            $('#recurringMonthsField').show();
        } else {
            $('#recurringMonthsField').hide();
        }
    }

    $('input[name="is_recurring"]').change(toggleRecurringMonths);
    toggleRecurringMonths();
});
</script>
@endpush

@endsection
