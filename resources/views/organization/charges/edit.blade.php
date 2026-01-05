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

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('organization.charges.update', $charge) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Charge Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title', $charge->title) }}"
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
                              class="summernote">{{ old('description', $charge->description) }}</textarea>
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

                <!-- Recurring -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Recurring <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="is_recurring" value="1"
                                   {{ old('is_recurring', $charge->is_recurring) == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">YES</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="is_recurring" value="0"
                                   {{ old('is_recurring', $charge->is_recurring) == '0' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">NO, One-Time ONLY</span>
                        </label>
                    </div>
                </div>

                <!-- Recurring Months (shown when YES is selected) -->
                <div id="recurringMonthsField" style="display: none;">
                    <label for="recurring_months" class="block text-sm font-medium text-gray-700 mb-1">
                        Every (months) <span class="text-red-500">*</span>
                    </label>
                    <select name="recurring_months" id="recurring_months"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('recurring_months', $charge->recurring_months) == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'month' : 'months' }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $charge->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $charge->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Scheduled Date -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Scheduled Date & Time
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                           value="{{ old('scheduled_at', $charge->scheduled_at ? $charge->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Reject Reason (if rejected) -->
                @if($charge->approval_status == 'rejected' && $charge->reject_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                    <p class="text-sm text-red-700">{{ $charge->reject_reason }}</p>
                </div>
                @endif

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $charge->image ? 'Replace Image' : 'Image' }}
                    </label>

                    @if($charge->image)
                    <!-- Current Image Preview -->
                    <div class="mb-3 flex items-center space-x-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <img src="{{ asset('storage/' . $charge->image) }}"
                             alt="{{ $charge->title }}"
                             class="w-16 h-16 rounded-lg object-cover border border-gray-300"
                             id="current-image-preview">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Current Image</p>
                            <p class="text-xs text-gray-500" id="current-file-name">{{ basename($charge->image) }}</p>
                        </div>
                        <button type="button" onclick="removeCurrentImage()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                    @endif

                    <!-- Upload New Image -->
                    <div class="relative">
                        <input type="file"
                               name="image"
                               id="image"
                               accept="image/*"
                               class="hidden"
                               onchange="updateFileName(this)">
                        <label for="image" id="upload-area" class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition @error('image') border-red-500 @enderror">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500" id="file-name">
                                    @if($charge->image)
                                        Click to replace with a new image
                                    @else
                                        PNG, JPG, GIF up to 2MB
                                    @endif
                                </p>
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
                    Update Charge
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
// Update filename display when file is selected
function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    const currentImagePreview = document.getElementById('current-image-preview');

    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `New: ${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('font-medium', 'text-green-600');

        // Show preview of new image
        if (currentImagePreview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                currentImagePreview.src = e.target.result;
                document.getElementById('current-file-name').textContent = fileName;
            }
            reader.readAsDataURL(input.files[0]);
        }
    } else {
        @if($charge->image)
            fileNameDisplay.textContent = 'Click to replace with a new image';
        @else
            fileNameDisplay.textContent = 'PNG, JPG, GIF up to 2MB';
        @endif
        fileNameDisplay.classList.remove('font-medium', 'text-green-600');
    }
}

// Remove current image
function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        const currentImageSection = document.getElementById('current-image-preview').closest('.mb-3');
        currentImageSection.style.display = 'none';
        document.getElementById('file-name').textContent = 'PNG, JPG, GIF up to 2MB';

        // Add hidden input to signal image removal
        const form = document.querySelector('form');
        let removeInput = document.getElementById('remove_image');
        if (!removeInput) {
            removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_image';
            removeInput.id = 'remove_image';
            removeInput.value = '1';
            form.appendChild(removeInput);
        }
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
