@extends('layouts.dashboard')

@section('page-title', 'Edit Member')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Members', 'url' => route('organization.members.index')],
    ['label' => 'Edit Member', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Member</h1>
            <p class="text-sm text-gray-500 mt-1">Update member details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organization.members.show', $member) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                View Details
            </a>
            <a href="{{ route('organization.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('organization.members.update', $member) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100">
        @csrf
        @method('PUT')

        <!-- Member Information Section -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Member Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Membership Details Section -->
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Membership Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Role</label>
                    <input type="text" name="role" value="{{ old('role', $membershipData['role'] ?? 'member') }}" placeholder="e.g., Member, Volunteer"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Membership Number</label>
                    <input type="text" name="membership_number" value="{{ old('membership_number', $membershipData['membership_number'] ?? '') }}" placeholder="e.g., VCC-1234"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Joined Date</label>
                    <input type="date" name="joined_at" value="{{ old('joined_at', $membershipData['joined_at'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional notes about this membership..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes', $membershipData['notes'] ?? '') }}</textarea>
            </div>
        </div>

        <!-- Type-Specific Details Section -->
        @if($organization && $organization->organizationType)
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">
                @if($organization->organizationType->slug === 'car_club')
                    🚗 Vehicle Details
                @elseif($organization->organizationType->slug === 'residential_club')
                    🏠 Residential Details
                @elseif($organization->organizationType->slug === 'sports_club')
                    ⚽ Sports Club Details
                @else
                    Additional Details
                @endif
            </h3>
            <p class="text-xs text-gray-500 mt-1">Optional information specific to this organization type</p>
        </div>
        <div class="p-6">
            @if($organization->organizationType->slug === 'car_club')
            <!-- Car Club Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Brand</label>
                    <input type="text" name="car_brand" value="{{ old('car_brand', $member->car_brand) }}" placeholder="e.g., Honda"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Model</label>
                    <input type="text" name="car_model" value="{{ old('car_model', $member->car_model) }}" placeholder="e.g., Civic"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Plate</label>
                    <input type="text" name="car_plate" value="{{ old('car_plate', $member->car_plate) }}" placeholder="e.g., ABC 1234"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Color</label>
                    <input type="text" name="car_color" value="{{ old('car_color', $member->car_color) }}" placeholder="e.g., Red"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Car Year</label>
                    <input type="number" name="car_year" value="{{ old('car_year', $member->car_year) }}" placeholder="e.g., 2020" min="1900" max="2099"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            @elseif($organization->organizationType->slug === 'residential_club')
            <!-- Residential Club Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Unit Number</label>
                    <input type="text" name="unit_number" value="{{ old('unit_number', $membershipData['unit_number'] ?? '') }}" placeholder="e.g., A-12-05"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Block</label>
                    <input type="text" name="block" value="{{ old('block', $membershipData['block'] ?? '') }}" placeholder="e.g., Block A"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Floor</label>
                    <input type="text" name="floor" value="{{ old('floor', $membershipData['floor'] ?? '') }}" placeholder="e.g., 12"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Address</label>
                    <input type="text" name="address_line_1" value="{{ old('address_line_1', $membershipData['address_line_1'] ?? '') }}" placeholder="Address Line 1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Postcode</label>
                    <input type="text" name="postcode" value="{{ old('postcode', $membershipData['postcode'] ?? '') }}" placeholder="e.g., 50450"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $membershipData['city'] ?? '') }}" placeholder="e.g., Kuala Lumpur"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">State</label>
                    <input type="text" name="state" value="{{ old('state', $membershipData['state'] ?? '') }}" placeholder="e.g., Wilayah Persekutuan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            @elseif($organization->organizationType->slug === 'sports_club')
            <!-- Sports Club Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $membershipData['emergency_contact_name'] ?? '') }}" placeholder="e.g., John Doe"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $membershipData['emergency_contact_phone'] ?? '') }}" placeholder="e.g., +60123456789"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Blood Type</label>
                    <select name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Blood Type</option>
                        <option value="A+" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_type', $membershipData['blood_type'] ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Preferred Sports</label>
                    <input type="text" name="preferred_sports" value="{{ old('preferred_sports', $membershipData['preferred_sports'] ?? '') }}" placeholder="e.g., Football, Basketball"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Medical Conditions</label>
                    <textarea name="medical_conditions" rows="2" placeholder="Any medical conditions or allergies..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('medical_conditions', $membershipData['medical_conditions'] ?? '') }}</textarea>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('organization.members.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Update Member
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
