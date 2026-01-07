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
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Transaction
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                    <a href="{{ route('organization.members.transactions.create', ['member' => $member, 'type' => 'payment']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Payment
                    </a>
                    <a href="{{ route('organization.members.transactions.create', ['member' => $member, 'type' => 'refund']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Add Refund
                    </a>
                </div>
            </div>
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
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </p>
                        </div>
                        @if($member->organizations->first())
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Role</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($member->organizations->first()->pivot->role ?? 'Member') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Membership Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->organizations->first()->pivot->membership_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($member->organizations->first()->pivot->joined_at)->format('d M Y') }}</p>
                        </div>
                        @if($member->organizations->first()->pivot->notes)
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->organizations->first()->pivot->notes }}</p>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            @if($member->organizations->first() && $member->organizations->first()->organizationType)
            @php $org = $member->organizations->first(); @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">
                        @if($org->organizationType->slug === 'car_club')
                            🚗 Vehicle Details
                        @elseif($org->organizationType->slug === 'residential_club')
                            🏠 Residential Details
                        @elseif($org->organizationType->slug === 'sports_club')
                            ⚽ Sports Club Details
                        @else
                            Additional Details
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    @if($org->organizationType->slug === 'car_club')
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
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Color</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_color ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Car Year</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->car_year ?? '-' }}</p>
                        </div>
                    </div>
                    @elseif($org->organizationType->slug === 'residential_club')
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Unit Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->unit_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Block</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->block ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Floor</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->floor ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->address_line_1 ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Postcode</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->postcode ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">City</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->city ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">State</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->state ?? '-' }}</p>
                        </div>
                    </div>
                    @elseif($org->organizationType->slug === 'sports_club')
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Emergency Contact</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->emergency_contact_name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Contact Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->emergency_contact_phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Blood Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->blood_type ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Preferred Sports</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->preferred_sports ?? '-' }}</p>
                        </div>
                        @if($org->details->medical_conditions)
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Medical Conditions</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $org->details->medical_conditions }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Payment Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="py-3 border-b border-gray-100">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Payment</span>
                            @if($lastPayment)
                            <p class="mt-1 text-sm font-semibold text-gray-900">RM {{ number_format($lastPayment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $lastPayment->created_at->format('d/m/Y H:i') }}</p>
                            @else
                            <p class="mt-1 text-sm text-gray-500">No payments yet</p>
                            @endif
                        </div>

                        <div class="py-3 border-b border-gray-100">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Next Renewal</span>
                            @if($nextRenewal)
                            <p class="mt-1 text-sm font-semibold text-gray-900">RM {{ number_format($nextRenewal['amount'], 2) }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($nextRenewal['date'])->format('d/m/Y H:i') }}</p>
                            @else
                            <p class="mt-1 text-sm text-gray-500">-</p>
                            @endif
                        </div>

                        <div class="py-3 border-b border-gray-100">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Recurring Charges/Plans</span>
                            @if($recurringCharges->count() > 0)
                            <div class="mt-2 space-y-2">
                                @foreach($recurringCharges as $charge)
                                <div class="text-sm">
                                    <p class="text-gray-500 text-xs">
                                        @if($charge->recurring_frequency === 'one-time')
                                            One-time
                                        @else
                                            {{ ucwords(str_replace('-', ' ', $charge->recurring_frequency)) }}
                                        @endif
                                    </p>
                                    <p class="font-medium text-gray-900">{{ $charge->title }}</p>
                                    <p class="text-gray-600">RM {{ number_format($charge->pivot->amount, 2) }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="mt-1 text-sm text-gray-500">No charges assigned</p>
                            @endif
                        </div>

                        <div class="py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Payment Status</span>
                            @php $paymentStatus = $member->payment_status; @endphp
                            <p class="mt-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $paymentStatus['color'] === 'red' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $paymentStatus['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $paymentStatus['color'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $paymentStatus['color'] === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ $paymentStatus['label'] }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
