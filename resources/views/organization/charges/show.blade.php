@extends('layouts.dashboard')

@section('title', 'Charge Details')
@section('page-title', 'Charge Details')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Charges', 'url' => route('organization.charges.index')],
    ['label' => 'View', 'url' => null]
]])
<div class="space-y-4">
    <!-- Back Button -->
    <div>
        <a href="{{ route('organization.charges.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Charges
        </a>
    </div>

    <!-- Charge Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-start space-x-4">
                @if($charge->image)
                <img src="{{ asset('storage/' . $charge->image) }}"
                     alt="{{ $charge->title }}"
                     class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                @else
                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-600">{{ strtoupper(substr($charge->title, 0, 2)) }}</span>
                </div>
                @endif
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $charge->title }}</h3>
                    <div class="text-sm text-gray-500 mt-1">{!! $charge->description !!}</div>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $charge->is_recurring ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            @if($charge->is_recurring)
                                Recurring ({{ $charge->recurring_months }} {{ $charge->recurring_months == 1 ? 'month' : 'months' }})
                            @else
                                One-Time
                            @endif
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($charge->approval_status == 'draft') bg-gray-100 text-gray-700
                            @elseif($charge->approval_status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($charge->approval_status == 'approved') bg-green-100 text-green-700
                            @elseif($charge->approval_status == 'rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($charge->approval_status) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $charge->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($charge->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Amount</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($charge->amount, 2) }}</p>
            </div>
        </div>

        <!-- Rejection Notice -->
        @if($charge->approval_status == 'rejected' && $charge->reject_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-red-800 mb-1">Charge Rejected</h4>
                    <p class="text-sm text-red-700">{{ $charge->reject_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">Please make the necessary changes and resubmit for approval.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="flex items-center space-x-3 pt-6 border-t border-gray-200">
            @if($charge->approval_status == 'pending')
                <div class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200">
                    Pending Approval
                </div>
            @elseif($charge->approval_status == 'rejected')
                <form method="POST" action="{{ route('organization.charges.submit', $charge) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        Resubmit for Approval
                    </button>
                </form>
            @elseif($charge->approval_status == 'draft')
                <form method="POST" action="{{ route('organization.charges.submit', $charge) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        Submit for Approval
                    </button>
                </form>
            @endif

            @if($charge->approval_status != 'pending')
            <a href="{{ route('organization.charges.edit', $charge) }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                Edit Charge
            </a>
            @endif

            <form id="delete-charge-{{ $charge->id }}" method="POST" action="{{ route('organization.charges.destroy', $charge) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button"
                    onclick="return confirmDelete('delete-charge-{{ $charge->id }}', '{{ addslashes($charge->title) }}')"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                Delete Charge
            </button>
        </div>
    </div>

    <!-- Mobile App Preview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mobile App Preview</h3>

        <div class="flex justify-center">
            <div class="w-80 bg-gray-100 rounded-3xl p-4 shadow-xl">
                <!-- Phone Frame -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
                    <!-- Status Bar -->
                    <div class="bg-gray-900 px-6 py-2 flex justify-between items-center text-white text-xs">
                        <span>9:41</span>
                        <div class="flex space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                        </div>
                    </div>

                    <!-- App Content -->
                    <div class="p-4 space-y-4" style="height: 600px; overflow-y: auto;">
                        <!-- Charge Card -->
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                            @if($charge->image)
                            <img src="{{ asset('storage/' . $charge->image) }}"
                                 alt="{{ $charge->title }}"
                                 class="w-full h-32 object-cover rounded-lg mb-3">
                            @endif
                            <h4 class="font-bold text-lg mb-1">{{ $charge->title }}</h4>
                            <p class="text-2xl font-bold">RM {{ number_format($charge->amount, 2) }}</p>
                            <p class="text-xs mt-2 opacity-90">
                                @if($charge->is_recurring)
                                    Recurring every {{ $charge->recurring_months }} {{ $charge->recurring_months == 1 ? 'month' : 'months' }}
                                @else
                                    One-time payment
                                @endif
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Description</p>
                            <div class="text-xs text-gray-600">{!! $charge->description !!}</div>
                        </div>

                        <!-- Pay Button -->
                        <button class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg">
                            Pay Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection