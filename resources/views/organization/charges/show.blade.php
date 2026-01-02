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
                @if($charge->image_path)
                <img src="{{ asset('storage/' . $charge->image_path) }}"
                     alt="{{ $charge->name }}"
                     class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                @else
                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-600">{{ strtoupper(substr($charge->name, 0, 2)) }}</span>
                </div>
                @endif
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $charge->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $charge->description }}</p>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $charge->type == 'monthly' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $charge->type == 'yearly' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $charge->type == 'one-time' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst($charge->type) }}
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

        <div class="flex items-center space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('organization.charges.edit', $charge) }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Edit Charge
            </a>
            <form id="delete-charge-{{ $charge->id }}" method="POST" action="{{ route('organization.charges.destroy', $charge) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button"
                    onclick="return confirmDelete('delete-charge-{{ $charge->id }}', '{{ addslashes($charge->name) }}')"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                Delete Charge
            </button>
        </div>
    </div>

    <!-- Members with this Charge -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Members ({{ $charge->members->count() }})</h3>

        @if($members->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($members as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">RM {{ number_format($member->pivot->amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $member->pivot->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $member->pivot->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $member->pivot->status == 'overdue' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($member->pivot->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">
                                {{ $member->pivot->paid_at ? \Carbon\Carbon::parse($member->pivot->paid_at)->format('M d, Y') : '-' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
        <div class="mt-4">
            {{ $members->links() }}
        </div>
        @endif
        @else
        <p class="text-sm text-gray-500 text-center py-8">No members assigned to this charge yet.</p>
        @endif
    </div>
</div>
@endsection