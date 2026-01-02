@extends('layouts.dashboard')

@section('title', 'Edit Announcement')
@section('page-title', 'Edit Announcement')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Announcements', 'url' => route('organization.announcements.index')],
    ['label' => 'Edit', 'url' => null]
]])
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Edit Announcement</h3>
            <p class="text-sm text-gray-500 mt-1">Update announcement details</p>
        </div>

        <form method="POST" action="{{ route('organization.announcements.update', $announcement) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title', $announcement->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content"
                              id="content"
                              rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror"
                              required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Scheduled At -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Schedule For (Optional)</label>
                    <input type="datetime-local"
                           name="scheduled_at"
                           id="scheduled_at"
                           value="{{ old('scheduled_at', $announcement->scheduled_at ? $announcement->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to publish immediately</p>
                </div>

                <!-- Publish Status -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="is_published"
                           id="is_published"
                           value="1"
                           {{ old('is_published', $announcement->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_published" class="ml-2 text-sm text-gray-700">
                        Publish immediately
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.announcements.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Update Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 400,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});
</script>
@endpush
