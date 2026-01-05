<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Charge;
use App\Models\Transaction;
use App\Exports\MembersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $organization = \App\Models\Organization::with('organizationType')->find($orgId);

        // Get members who belong to this organization via pivot table
        $query = Member::whereHas('organizations', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->with([
            'organizations' => function($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            },
            'charges' => function($q) {
                $q->withPivot('amount', 'status', 'paid_at', 'next_renewal_date');
            }
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('organizations', function($q) use ($orgId, $request) {
                $q->where('organization_id', $orgId)
                  ->where('member_organization.status', $request->status);
            });
        }

        $members = $query->withCount('charges')->latest()->paginate(15);

        // Load type-specific details for each member
        foreach ($members as $member) {
            if ($member->organizations->first() && $organization->organizationType) {
                $pivotId = \DB::table('member_organization')
                    ->where('member_id', $member->id)
                    ->where('organization_id', $orgId)
                    ->value('id');

                if ($pivotId) {
                    switch ($organization->organizationType->slug) {
                        case 'car_club':
                            $member->typeDetails = \App\Models\MemberOrganizationCarDetail::where('member_organization_id', $pivotId)->first();
                            break;
                        case 'residential_club':
                            $member->typeDetails = \App\Models\MemberOrganizationResidentialDetail::where('member_organization_id', $pivotId)->first();
                            break;
                        case 'sports_club':
                            $member->typeDetails = \App\Models\MemberOrganizationSportsDetail::where('member_organization_id', $pivotId)->first();
                            break;
                    }
                }
            }
        }

        return view('organization.members.index', compact('members', 'organization'));
    }

    public function create()
    {
        $organization = \App\Models\Organization::with('organizationType')->find(auth()->user()->organization_id);
        return view('organization.members.create', compact('organization'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'role' => 'nullable|string|max:100',
            'joined_at' => 'nullable|date',
            'membership_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $orgId = auth()->user()->organization_id;
        $organization = \App\Models\Organization::with('organizationType')->find($orgId);

        // Create member
        $member = Member::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'organization_id' => $orgId, // Keep for legacy support
            'status' => $validated['status'],
        ]);

        // Attach to organization via pivot table
        $member->organizations()->attach($orgId, [
            'joined_at' => $validated['joined_at'] ?? now(),
            'status' => $validated['status'],
            'role' => $validated['role'] ?? 'member',
            'membership_number' => $validated['membership_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Get the pivot ID for type-specific details
        $pivotId = \DB::table('member_organization')
            ->where('member_id', $member->id)
            ->where('organization_id', $orgId)
            ->value('id');

        // Save type-specific details based on organization type
        if ($pivotId && $organization->organizationType) {
            $this->saveTypeSpecificDetails($pivotId, $organization->organizationType->slug, $request->all());
        }

        return redirect()->route('organization.members.index')
            ->with('success', 'Member added successfully.');
    }

    public function show(Member $member)
    {
        $orgId = auth()->user()->organization_id;

        // Check if member belongs to this organization
        if (!$member->organizations()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        $member->load(['charges' => function($q) {
            $q->withPivot('amount', 'status', 'paid_at', 'next_renewal_date');
        }, 'transactions', 'organizations' => function($q) use ($orgId) {
            $q->where('organization_id', $orgId)->with('organizationType');
        }]);

        // Load type-specific details for this organization
        $organization = $member->organizations->first();
        if ($organization) {
            $pivotId = \DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $orgId)
                ->value('id');

            if ($pivotId && $organization->organizationType) {
                switch ($organization->organizationType->slug) {
                    case 'car_club':
                        $organization->details = \App\Models\MemberOrganizationCarDetail::where('member_organization_id', $pivotId)->first();
                        break;
                    case 'residential_club':
                        $organization->details = \App\Models\MemberOrganizationResidentialDetail::where('member_organization_id', $pivotId)->first();
                        break;
                    case 'sports_club':
                        $organization->details = \App\Models\MemberOrganizationSportsDetail::where('member_organization_id', $pivotId)->first();
                        break;
                }
            }
        }

        // Get last payment
        $lastPayment = $member->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->latest()
            ->first();

        // Get next renewal info
        $nextRenewalCharge = $member->charges()
            ->whereIn('recurring_frequency', ['monthly', 'bi-monthly', 'semi-annually', 'annually'])
            ->wherePivot('next_renewal_date', '>=', now())
            ->orderBy('charge_member.next_renewal_date', 'asc')
            ->first();

        $nextRenewal = null;
        if ($nextRenewalCharge) {
            $nextRenewal = [
                'amount' => $nextRenewalCharge->pivot->amount,
                'date' => $nextRenewalCharge->pivot->next_renewal_date
            ];
        }

        // Get all recurring charges
        $recurringCharges = $member->charges;

        return view('organization.members.show', compact('member', 'lastPayment', 'nextRenewal', 'recurringCharges'));
    }

    public function edit(Member $member)
    {
        $orgId = auth()->user()->organization_id;

        // Check if member belongs to this organization
        if (!$member->organizations()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        $organization = \App\Models\Organization::with('organizationType')->find($orgId);
        $member->load(['organizations' => function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        }]);

        // Get membership data with type-specific details
        $membershipData = null;
        if ($member->organizations->first()) {
            $org = $member->organizations->first();
            $pivotId = \DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $orgId)
                ->value('id');

            $membershipData = [
                'role' => $org->pivot->role,
                'joined_at' => \Carbon\Carbon::parse($org->pivot->joined_at)->format('Y-m-d'),
                'status' => $org->pivot->status,
                'membership_number' => $org->pivot->membership_number,
                'notes' => $org->pivot->notes,
            ];

            // Load type-specific details
            if ($pivotId && $organization->organizationType) {
                switch ($organization->organizationType->slug) {
                    case 'car_club':
                        $details = \App\Models\MemberOrganizationCarDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $membershipData['car_brand'] = $details->car_brand;
                            $membershipData['car_model'] = $details->car_model;
                            $membershipData['car_plate'] = $details->car_plate;
                            $membershipData['car_color'] = $details->car_color;
                            $membershipData['car_year'] = $details->car_year;
                        }
                        break;
                    case 'residential_club':
                        $details = \App\Models\MemberOrganizationResidentialDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $membershipData['unit_number'] = $details->unit_number;
                            $membershipData['block'] = $details->block;
                            $membershipData['floor'] = $details->floor;
                            $membershipData['address_line_1'] = $details->address_line_1;
                            $membershipData['address_line_2'] = $details->address_line_2;
                            $membershipData['postcode'] = $details->postcode;
                            $membershipData['city'] = $details->city;
                            $membershipData['state'] = $details->state;
                        }
                        break;
                    case 'sports_club':
                        $details = \App\Models\MemberOrganizationSportsDetail::where('member_organization_id', $pivotId)->first();
                        if ($details) {
                            $membershipData['emergency_contact_name'] = $details->emergency_contact_name;
                            $membershipData['emergency_contact_phone'] = $details->emergency_contact_phone;
                            $membershipData['blood_type'] = $details->blood_type;
                            $membershipData['medical_conditions'] = $details->medical_conditions;
                            $membershipData['preferred_sports'] = $details->preferred_sports;
                        }
                        break;
                }
            }
        }

        return view('organization.members.edit', compact('member', 'organization', 'membershipData'));
    }

    public function update(Request $request, Member $member)
    {
        $orgId = auth()->user()->organization_id;

        // Check if member belongs to this organization
        if (!$member->organizations()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'role' => 'nullable|string|max:100',
            'joined_at' => 'nullable|date',
            'membership_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Update member basic info
        $member->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        // Update pivot table data
        $member->organizations()->updateExistingPivot($orgId, [
            'joined_at' => $validated['joined_at'] ?? $member->organizations->first()->pivot->joined_at,
            'status' => $validated['status'],
            'role' => $validated['role'] ?? 'member',
            'membership_number' => $validated['membership_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Get pivot ID and update type-specific details
        $pivotId = \DB::table('member_organization')
            ->where('member_id', $member->id)
            ->where('organization_id', $orgId)
            ->value('id');

        $organization = \App\Models\Organization::with('organizationType')->find($orgId);
        if ($pivotId && $organization->organizationType) {
            $this->saveTypeSpecificDetails($pivotId, $organization->organizationType->slug, $request->all());
        }

        return redirect()->route('organization.members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        if ($member->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $member->delete();

        return redirect()->route('organization.members.index')
            ->with('success', 'Member deleted successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id',
            'status' => 'required|in:active,inactive',
        ]);

        Member::whereIn('id', $request->ids)
            ->where('organization_id', auth()->user()->organization_id)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id',
        ]);

        Member::whereIn('id', $request->ids)
            ->where('organization_id', auth()->user()->organization_id)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function exportCsv()
    {
        $orgId = auth()->user()->organization_id;
        $members = Member::whereHas('organizations', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->with(['organizations' => function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        }])->withCount('charges')->get();

        $filename = 'members_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($members) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Name', 'Email', 'Phone', 'Role', 'Membership Number', 'Status', 'Charges Count', 'Joined Date']);

            // Add data rows
            foreach ($members as $member) {
                $org = $member->organizations->first();
                fputcsv($file, [
                    $member->name,
                    $member->email,
                    $member->phone ?? '',
                    $org ? ucfirst($org->pivot->role ?? 'Member') : '',
                    $org ? ($org->pivot->membership_number ?? '') : '',
                    $member->status,
                    $member->charges_count,
                    $org ? \Carbon\Carbon::parse($org->pivot->joined_at)->format('Y-m-d H:i:s') : $member->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel()
    {
        $filename = 'members_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new MembersExport(auth()->user()->organization_id), $filename);
    }

    public function exportPdf()
    {
        $members = Member::where('organization_id', auth()->user()->organization_id)
            ->withCount('charges')
            ->get();

        $pdf = Pdf::loadView('organization.members.pdf', compact('members'));
        return $pdf->download('members_' . date('Y-m-d_His') . '.pdf');
    }

    private function saveTypeSpecificDetails($pivotId, $typeSlug, $data)
    {
        switch ($typeSlug) {
            case 'car_club':
                \App\Models\MemberOrganizationCarDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'car_brand' => $data['car_brand'] ?? null,
                        'car_model' => $data['car_model'] ?? null,
                        'car_plate' => $data['car_plate'] ?? null,
                        'car_color' => $data['car_color'] ?? null,
                        'car_year' => $data['car_year'] ?? null,
                    ]
                );
                break;

            case 'residential_club':
                \App\Models\MemberOrganizationResidentialDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'unit_number' => $data['unit_number'] ?? null,
                        'block' => $data['block'] ?? null,
                        'floor' => $data['floor'] ?? null,
                        'address_line_1' => $data['address_line_1'] ?? null,
                        'address_line_2' => $data['address_line_2'] ?? null,
                        'postcode' => $data['postcode'] ?? null,
                        'city' => $data['city'] ?? null,
                        'state' => $data['state'] ?? null,
                    ]
                );
                break;

            case 'sports_club':
                \App\Models\MemberOrganizationSportsDetail::updateOrCreate(
                    ['member_organization_id' => $pivotId],
                    [
                        'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                        'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                        'blood_type' => $data['blood_type'] ?? null,
                        'medical_conditions' => $data['medical_conditions'] ?? null,
                        'preferred_sports' => $data['preferred_sports'] ?? null,
                    ]
                );
                break;
        }
    }

    public function createTransaction(Member $member, Request $request)
    {
        $orgId = auth()->user()->organization_id;

        if (!$member->organizations()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        $type = $request->query('type', 'payment');
        $charges = Charge::where('organization_id', $orgId)
            ->where('status', 'active')
            ->get();

        return view('organization.members.create-transaction', compact('member', 'type', 'charges'));
    }

    public function storeTransaction(Request $request, Member $member)
    {
        $orgId = auth()->user()->organization_id;

        if (!$member->organizations()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:payment,refund',
            'charge_id' => 'required|exists:charges,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $transaction = Transaction::create([
            'organization_id' => $orgId,
            'member_id' => $member->id,
            'charge_id' => $validated['charge_id'],
            'transaction_number' => 'TXN-' . strtoupper(uniqid()),
            'amount' => $validated['type'] === 'refund' ? -abs($validated['amount']) : abs($validated['amount']),
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
            'notes' => $validated['notes'] ?? null,
            'receipt' => $receiptPath,
        ]);

        return redirect()->route('organization.members.show', $member)
            ->with('success', ucfirst($validated['type']) . ' added successfully.');
    }
}
