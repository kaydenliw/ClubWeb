@extends('layouts.dashboard')

@section('title', 'Announcement Details')
@section('page-title', 'Announcement Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Announcements', 'url' => route('organization.announcements.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $announcement->title }}</h2>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $announcement->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $announcement->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>Created: {{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                    @if($announcement->published_at)
                    <span>Published: {{ $announcement->published_at->format('M d, Y h:i A') }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('organization.announcements.edit', $announcement) }}"
                   class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Edit
                </a>
                <a href="{{ route('organization.announcements.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Content</h3>
        <div class="prose max-w-none text-gray-700">
            {!! $announcement->content !!}
        </div>
    </div>

    <!-- Additional Info Card -->
    @if($announcement->scheduled_at)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Schedule Information</h3>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Scheduled for: {{ $announcement->scheduled_at->format('M d, Y h:i A') }}</span>
        </div>
    </div>
    @endif
</div>
@endsection
