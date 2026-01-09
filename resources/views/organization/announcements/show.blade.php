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
                        @if($announcement->approval_status == 'pending_approval') bg-yellow-100 text-yellow-700
                        @elseif(in_array($announcement->approval_status, ['approved_pending_publish', 'approved_published'])) bg-green-100 text-green-700
                        @elseif($announcement->approval_status == 'rejected') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucwords(str_replace('_', ' ', $announcement->approval_status)) }}
                    </span>
                    @if($announcement->is_highlighted)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700" title="This announcement is highlighted">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Highlighted
                    </span>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>Created: {{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                    @if($announcement->published_at)
                    <span>Published: {{ $announcement->published_at->format('M d, Y h:i A') }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($announcement->approval_status == 'pending_approval')
                    <div class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200">
                        Pending Approval
                    </div>
                @elseif($announcement->approval_status == 'rejected')
                    <form method="POST" action="{{ route('organization.announcements.submit', $announcement) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            Resubmit for Approval
                        </button>
                    </form>
                @elseif($announcement->approval_status == 'draft')
                    <form method="POST" action="{{ route('organization.announcements.submit', $announcement) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            Submit for Approval
                        </button>
                    </form>
                @endif

                @if($announcement->approval_status != 'pending_approval')
                <a href="{{ route('organization.announcements.edit', $announcement) }}"
                   class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Edit
                </a>
                @endif
                <a href="{{ route('organization.announcements.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Rejection Notice -->
    @if($announcement->approval_status == 'rejected' && $announcement->reject_reason)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-red-800 mb-1">Announcement Rejected</h4>
                <p class="text-sm text-red-700">{{ $announcement->reject_reason }}</p>
                <p class="text-xs text-red-600 mt-2">Please make the necessary changes and resubmit for approval.</p>
            </div>
        </div>
    </div>
    @endif

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
