@extends('layouts.dashboard')

@section('title', 'Announcement Details')
@section('page-title', 'Announcement Details & Approval')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Announcements', 'url' => route('admin.announcements.index')],
    ['label' => 'Details', 'url' => null]
]])
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (Left - 2 columns) -->
    <div class="lg:col-span-2 space-y-4">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.announcements.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Announcements
        </a>
    </div>

    <!-- Announcement Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900">{{ $announcement->title }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $announcement->organization->name }}</p>
                <div class="flex items-center space-x-3 mt-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($announcement->approval_status == 'draft') bg-gray-100 text-gray-700
                        @elseif($announcement->approval_status == 'pending_approval') bg-yellow-100 text-yellow-700
                        @elseif($announcement->approval_status == 'approved_pending_publish') bg-blue-100 text-blue-700
                        @elseif($announcement->approval_status == 'approved_published') bg-green-100 text-green-700
                        @elseif($announcement->approval_status == 'rejected') bg-red-100 text-red-700
                        @endif">
                        {{ ucwords(str_replace('_', ' ', $announcement->approval_status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-2">Content</h4>
            <div class="text-sm text-gray-700 prose max-w-none">{!! $announcement->content !!}</div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 pt-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Organization</p>
                <p class="text-sm text-gray-900 mt-1">{{ $announcement->organization->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Created By</p>
                <p class="text-sm text-gray-900 mt-1">{{ $announcement->creator->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Publish Date</p>
                <p class="text-sm text-gray-900 mt-1">
                    {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, h:i A') : 'Not set' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Created Date</p>
                <p class="text-sm text-gray-900 mt-1">{{ $announcement->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        @if($announcement->approval_status == 'rejected' && $announcement->reject_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
            <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
            <p class="text-sm text-red-700">{{ $announcement->reject_reason }}</p>
        </div>
        @endif
    </div>
    </div>

    <!-- Right Sidebar (1 column) -->
    <div class="lg:col-span-1 space-y-4">
        <!-- Approval Actions -->
        @if($announcement->approval_status == 'pending_approval')
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 lg:sticky lg:top-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Actions</h3>
            <div class="space-y-4">
                <!-- Approve -->
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">Approve Announcement</h4>
                    <p class="text-xs text-green-700 mb-4">Organization can publish after approval.</p>
                    <form method="POST" action="{{ route('admin.announcements.approve', $announcement) }}"
                          onsubmit="return confirm('Approve this announcement?')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            Approve
                        </button>
                    </form>
                </div>

                <!-- Reject -->
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Reject Announcement</h4>
                    <p class="text-xs text-red-700 mb-4">Provide a reason for rejection.</p>
                    <form method="POST" action="{{ route('admin.announcements.reject', $announcement) }}" id="rejectAnnouncementForm">
                        @csrf
                        @method('PUT')
                        <textarea name="reject_reason" rows="3" required placeholder="Enter rejection reason..."
                                  class="w-full px-3 py-2 text-sm border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 mb-3"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
