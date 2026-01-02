@extends('layouts.dashboard')

@section('page-title', 'Activity Logs')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Activity Logs', 'url' => null]
]])
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Activity Logs</h3>
            <p class="text-sm text-gray-500 mt-1">Track all activities and changes in your organization</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">User</label>
                <select name="user_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Apply Filters
                </button>
                @if(request()->hasAny(['action', 'user_id', 'start_date', 'end_date']))
                <a href="{{ route('organization.activity-logs.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    Clear Filters
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->user->email ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ str_contains($log->action, 'create') ? 'bg-green-100 text-green-700' : '' }}
                                {{ str_contains($log->action, 'update') ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ str_contains($log->action, 'delete') ? 'bg-red-100 text-red-700' : '' }}
                                {{ !str_contains($log->action, 'create') && !str_contains($log->action, 'update') && !str_contains($log->action, 'delete') ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $log->description }}</div>
                            @if($log->model_type)
                            <div class="text-xs text-gray-500 mt-1">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                            No activity logs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
