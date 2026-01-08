<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Organization;
use App\Exports\AdminMembersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['organization', 'organizations']);

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $members = $query->latest()->get();
        $organizations = Organization::orderBy('name')->get();

        return view('admin.members.index', compact('members', 'organizations'));
    }

    public function show(Member $member)
    {
        $member->load(['organization', 'organizations.organizationType', 'transactions.organization', 'charges', 'contactTickets.organization']);

        // Load type-specific details for each organization membership
        foreach ($member->organizations as $org) {
            $pivotId = \DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $org->id)
                ->value('id');

            if ($pivotId && $org->organizationType) {
                switch ($org->organizationType->slug) {
                    case 'car_club':
                        $org->details = \App\Models\MemberOrganizationCarDetail::where('member_organization_id', $pivotId)->first();
                        break;
                    case 'residential_club':
                        $org->details = \App\Models\MemberOrganizationResidentialDetail::where('member_organization_id', $pivotId)->first();
                        break;
                    case 'sports_club':
                        $org->details = \App\Models\MemberOrganizationSportsDetail::where('member_organization_id', $pivotId)->first();
                        break;
                }
            }
        }

        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $member->load(['organizations.organizationType']);
        $organizations = Organization::with('organizationType')->orderBy('name')->get();
        $organizationTypes = \App\Models\OrganizationType::all();

        // Prepare memberships data for JavaScript
        $membershipsData = $member->organizations->map(function($org) use ($member) {
            $pivotId = \DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $org->id)
                ->value('id');

            $data = [
                'organization_id' => $org->id,
                'role' => $org->pivot->role,
                'joined_at' => \Carbon\Carbon::parse($org->pivot->joined_at)->format('Y-m-d'),
                'status' => $org->pivot->status,
                'membership_number' => $org->pivot->membership_number,
                'notes' => $org->pivot->notes,
                'organization_type_slug' => $org->organizationType ? $org->organizationType->slug : null,
            ];

            // Load type-specific details
            if ($pivotId && $org->organizationType) {
                switch ($org->organizationType->slug) {
                    case 'car_club':
                        $details = \App\Models\MemberOrganizationCarDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $data['car_brand'] = $details->car_brand;
                            $data['car_model'] = $details->car_model;
                            $data['car_plate'] = $details->car_plate;
                            $data['car_color'] = $details->car_color;
                            $data['car_year'] = $details->car_year;
                        }
                        break;
                    case 'residential_club':
                        $details = \App\Models\MemberOrganizationResidentialDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $data['unit_number'] = $details->unit_number;
                            $data['block'] = $details->block;
                            $data['floor'] = $details->floor;
                            $data['address_line_1'] = $details->address_line_1;
                            $data['address_line_2'] = $details->address_line_2;
                            $data['postcode'] = $details->postcode;
                            $data['city'] = $details->city;
                            $data['state'] = $details->state;
                        }
                        break;
                    case 'sports_club':
                        $details = \App\Models\MemberOrganizationSportsDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $data['emergency_contact_name'] = $details->emergency_contact_name;
                            $data['emergency_contact_phone'] = $details->emergency_contact_phone;
                            $data['blood_type'] = $details->blood_type;
                            $data['medical_conditions'] = $details->medical_conditions;
                            $data['preferred_sports'] = $details->preferred_sports;
                        }
                        break;
                }
            }

            return $data;
        })->values();

        return view('admin.members.edit', compact('member', 'organizations', 'membershipsData', 'organizationTypes'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'organization_id' => 'required|exists:organizations,id',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $member->update($validated);

            // Handle multiple organization memberships
            if ($request->has('memberships')) {
                $syncData = [];
                $detailsToSave = [];

                foreach ($request->memberships as $membership) {
                    if (!empty($membership['organization_id'])) {
                        $syncData[$membership['organization_id']] = [
                            'joined_at' => $membership['joined_at'] ?? now(),
                            'status' => $membership['status'] ?? 'active',
                            'role' => $membership['role'] ?? 'member',
                            'membership_number' => $membership['membership_number'] ?? null,
                            'notes' => $membership['notes'] ?? null,
                        ];

                        // Store details for later processing
                        $detailsToSave[$membership['organization_id']] = $membership;
                    }
                }

                $member->organizations()->sync($syncData);

                // Save type-specific details
                foreach ($detailsToSave as $orgId => $membership) {
                    $pivotId = \DB::table('member_organization')
                        ->where('member_id', $member->id)
                        ->where('organization_id', $orgId)
                        ->value('id');

                    if ($pivotId) {
                        $org = Organization::find($orgId);
                        if ($org && $org->organizationType) {
                            $this->saveTypeSpecificDetails($pivotId, $org->organizationType->slug, $membership);
                        }
                    }
                }
            }

            return redirect()->route('admin.members.edit', $member)
                ->with('success', 'Member updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update member. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function exportCsv()
    {
        return Excel::download(new AdminMembersExport, 'admin_members_' . date('Y-m-d_His') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportExcel()
    {
        return Excel::download(new AdminMembersExport, 'admin_members_' . date('Y-m-d_His') . '.xlsx');
    }

    public function exportPdf()
    {
        $members = Member::with('organization')->get();
        $pdf = Pdf::loadView('admin.members.pdf', compact('members'));
        return $pdf->download('admin_members_' . date('Y-m-d_His') . '.pdf');
    }

    public function destroy(Member $member)
    {
        try {
            $member->delete();
            return redirect()->route('admin.members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.members.index')
                ->with('error', 'Failed to delete member. ' . $e->getMessage());
        }
    }

    private function saveTypeSpecificDetails($pivotId, $typeSlug, $membership)
    {
        switch ($typeSlug) {
            case 'car_club':
                \App\Models\MemberOrganizationCarDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'car_brand' => $membership['car_brand'] ?? null,
                        'car_model' => $membership['car_model'] ?? null,
                        'car_plate' => $membership['car_plate'] ?? null,
                        'car_color' => $membership['car_color'] ?? null,
                        'car_year' => $membership['car_year'] ?? null,
                    ]
                );
                break;

            case 'residential_club':
                \App\Models\MemberOrganizationResidentialDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'unit_number' => $membership['unit_number'] ?? null,
                        'block' => $membership['block'] ?? null,
                        'floor' => $membership['floor'] ?? null,
                        'address_line_1' => $membership['address_line_1'] ?? null,
                        'address_line_2' => $membership['address_line_2'] ?? null,
                        'postcode' => $membership['postcode'] ?? null,
                        'city' => $membership['city'] ?? null,
                        'state' => $membership['state'] ?? null,
                    ]
                );
                break;

            case 'sports_club':
                \App\Models\MemberOrganizationSportsDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'emergency_contact_name' => $membership['emergency_contact_name'] ?? null,
                        'emergency_contact_phone' => $membership['emergency_contact_phone'] ?? null,
                        'blood_type' => $membership['blood_type'] ?? null,
                        'medical_conditions' => $membership['medical_conditions'] ?? null,
                        'preferred_sports' => $membership['preferred_sports'] ?? null,
                    ]
                );
                break;
        }
    }
}