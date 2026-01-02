@extends('layouts.dashboard')

@section('page-title', 'Member Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $member->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Member since {{ $member->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organization.members.edit', $member) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Edit Member
            </a>
            <a href="{{ route('organization.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Member Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->phone ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->address ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Car Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Brand</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_brand ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Model</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_model ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Plate</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_plate ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Charges</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $member->charges->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-sm text-gray-600">Total Transactions</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $member->transactions->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
