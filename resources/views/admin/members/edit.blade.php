@extends('layouts.dashboard')

@section('page-title', 'Edit Member')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Members', 'url' => route('admin.members.index')],
    ['label' => 'Edit', 'url' => null]
]])
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Member</h1>
            <p class="text-sm text-gray-500 mt-1">Update member details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.members.show', $member) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                View Details
            </a>
            <a href="{{ route('admin.members.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.members.update', $member) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100">
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
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Organization *</label>
                    <select name="organization_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('organization_id') border-red-500 @enderror">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ old('organization_id', $member->organization_id) == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('organization_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500 mt-1">Primary organization (for legacy support)</p>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status *</label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Multiple Organizations Section -->
        <div class="px-6 py-4 border-b border-t border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Organization Memberships</h3>
            <p class="text-xs text-gray-500 mt-1">Manage which organizations this member belongs to</p>
        </div>
        <div class="p-6" x-data="organizationManager()">
            <div class="space-y-4">
                <template x-for="(membership, index) in memberships" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Organization *</label>
                                <select x-model="membership.organization_id" :name="'memberships[' + index + '][organization_id]'" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Organization</option>
                                    @foreach($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Role</label>
                                <input type="text" x-model="membership.role" :name="'memberships[' + index + '][role]'" placeholder="e.g., Member, Volunteer, Donor" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Joined Date *</label>
                                <input type="date" x-model="membership.joined_at" :name="'memberships[' + index + '][joined_at]'" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                                <select x-model="membership.status" :name="'memberships[' + index + '][status]'" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Membership Number</label>
                                <input type="text" x-model="membership.membership_number" :name="'memberships[' + index + '][membership_number]'" placeholder="e.g., VCC-1234" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="removeMembership(index)" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                    Remove
                                </button>
                            </div>

                            <!-- Type-specific Details Section -->
                            <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 mb-3">Additional Details (Optional)</h4>

                                <!-- Car Club Fields -->
                                <template x-if="getOrgType(membership.organization_id) === 'car_club'">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">🚗 Car Brand</label>
                                            <input type="text" x-model="membership.car_brand" :name="'memberships[' + index + '][car_brand]'" placeholder="e.g., Honda" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Car Model</label>
                                            <input type="text" x-model="membership.car_model" :name="'memberships[' + index + '][car_model]'" placeholder="e.g., Civic" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Car Plate</label>
                                            <input type="text" x-model="membership.car_plate" :name="'memberships[' + index + '][car_plate]'" placeholder="e.g., ABC 1234" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Car Color</label>
                                            <input type="text" x-model="membership.car_color" :name="'memberships[' + index + '][car_color]'" placeholder="e.g., Red" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Car Year</label>
                                            <input type="number" x-model="membership.car_year" :name="'memberships[' + index + '][car_year]'" placeholder="e.g., 2020" min="1900" max="2099" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </template>

                                <!-- Residential Club Fields -->
                                <template x-if="getOrgType(membership.organization_id) === 'residential_club'">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">🏠 Unit Number</label>
                                            <input type="text" x-model="membership.unit_number" :name="'memberships[' + index + '][unit_number]'" placeholder="e.g., A-12-05" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Block</label>
                                            <input type="text" x-model="membership.block" :name="'memberships[' + index + '][block]'" placeholder="e.g., Block A" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Floor</label>
                                            <input type="text" x-model="membership.floor" :name="'memberships[' + index + '][floor]'" placeholder="e.g., 12" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Address</label>
                                            <input type="text" x-model="membership.address_line_1" :name="'memberships[' + index + '][address_line_1]'" placeholder="Address Line 1" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Postcode</label>
                                            <input type="text" x-model="membership.postcode" :name="'memberships[' + index + '][postcode]'" placeholder="e.g., 50450" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </template>

                                <!-- Sports Club Fields -->
                                <template x-if="getOrgType(membership.organization_id) === 'sports_club'">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">⚽ Emergency Contact Name</label>
                                            <input type="text" x-model="membership.emergency_contact_name" :name="'memberships[' + index + '][emergency_contact_name]'" placeholder="e.g., John Doe" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Emergency Contact Phone</label>
                                            <input type="text" x-model="membership.emergency_contact_phone" :name="'memberships[' + index + '][emergency_contact_phone]'" placeholder="e.g., +60123456789" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Blood Type</label>
                                            <select x-model="membership.blood_type" :name="'memberships[' + index + '][blood_type]'" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Blood Type</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Preferred Sports</label>
                                            <input type="text" x-model="membership.preferred_sports" :name="'memberships[' + index + '][preferred_sports]'" placeholder="e.g., Football, Basketball" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Medical Conditions</label>
                                            <textarea x-model="membership.medical_conditions" :name="'memberships[' + index + '][medical_conditions]'" rows="2" placeholder="Any medical conditions or allergies..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-2">Notes</label>
                                <textarea x-model="membership.notes" :name="'memberships[' + index + '][notes]'" rows="2" placeholder="Additional notes about this membership..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <button type="button" @click="addMembership()" class="mt-4 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                + Add Organization Membership
            </button>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.members.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
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

@push('scripts')
<script>
const organizations = @json($organizations);
const organizationTypes = @json($organizationTypes);

// Debug: Check what data we have
console.log('Organizations:', organizations);
console.log('Organization Types:', organizationTypes);

function getOrgType(orgId) {
    const org = organizations.find(o => o.id == orgId);
    console.log('Looking for org ID:', orgId, 'Found:', org);
    if (!org || !org.organization_type) return null;
    console.log('Org type slug:', org.organization_type.slug);
    return org.organization_type.slug;
}

function organizationManager() {
    return {
        memberships: @json($membershipsData),
        getOrgType(orgId) {
            const org = organizations.find(o => o.id == orgId);
            console.log('Alpine getOrgType - Looking for org ID:', orgId, 'Found:', org);
            if (!org || !org.organization_type) {
                console.log('No org or no organization_type found');
                return null;
            }
            console.log('Returning slug:', org.organization_type.slug);
            return org.organization_type.slug;
        },
        addMembership() {
            this.memberships.push({
                organization_id: '',
                role: 'member',
                joined_at: new Date().toISOString().split('T')[0],
                status: 'active',
                membership_number: '',
                notes: '',
                // Car club fields
                car_brand: '',
                car_model: '',
                car_plate: '',
                car_color: '',
                car_year: '',
                // Residential club fields
                unit_number: '',
                block: '',
                floor: '',
                address_line_1: '',
                address_line_2: '',
                postcode: '',
                city: '',
                state: '',
                // Sports club fields
                emergency_contact_name: '',
                emergency_contact_phone: '',
                blood_type: '',
                medical_conditions: '',
                preferred_sports: ''
            });
        },
        removeMembership(index) {
            showConfirmModal(
                'Remove Membership',
                'Are you sure you want to remove this organization membership?',
                'Remove',
                () => {
                    this.memberships.splice(index, 1);
                }
            );
        }
    }
}

// Custom confirmation modal
function showConfirmModal(title, message, confirmText, onConfirm) {
    const modal = document.createElement('div');
    modal.id = 'customConfirmModal';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;';
    modal.innerHTML = `
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7);" onclick="closeConfirmModal()"></div>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 400px;">
            <div class="bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600">${message}</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button onclick="closeConfirmModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md transition">
                        Cancel
                    </button>
                    <button onclick="confirmAction()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition">
                        ${confirmText}
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    window.confirmAction = function() {
        closeConfirmModal();
        onConfirm();
    };
}

function closeConfirmModal() {
    const modal = document.getElementById('customConfirmModal');
    if (modal) {
        modal.remove();
    }
}
</script>
@endpush
