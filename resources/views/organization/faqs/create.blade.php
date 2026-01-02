@extends('layouts.dashboard')

@section('title', 'Create FAQ')
@section('page-title', 'Create FAQ')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'FAQs', 'url' => route('organization.faqs.index')],
    ['label' => 'Create', 'url' => null]
]])
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">New FAQ</h3>
            <p class="text-sm text-gray-500 mt-1">Create a new frequently asked question</p>
        </div>

        <form method="POST" action="{{ route('organization.faqs.store') }}">
            @csrf

            <div class="space-y-4">
                <!-- Question -->
                <div>
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                    <input type="text"
                           name="question"
                           id="question"
                           value="{{ old('question') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('question') border-red-500 @enderror"
                           required>
                    @error('question')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Answer -->
                <div>
                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">Answer</label>
                    <textarea name="answer"
                              id="answer"
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('answer') border-red-500 @enderror"
                              required>{{ old('answer') }}</textarea>
                    @error('answer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category"
                            id="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Category</option>
                        <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                        <option value="Membership" {{ old('category') == 'Membership' ? 'selected' : '' }}>Membership</option>
                        <option value="Payment" {{ old('category') == 'Payment' ? 'selected' : '' }}>Payment</option>
                        <option value="Events" {{ old('category') == 'Events' ? 'selected' : '' }}>Events</option>
                        <option value="Technical" {{ old('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                    </select>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number"
                           name="order"
                           id="order"
                           value="{{ old('order', 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.faqs.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Create FAQ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
