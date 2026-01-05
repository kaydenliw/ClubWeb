<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Exports\AdminOrganizationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::withCount(['members', 'charges'])
            ->with(['transactions' => function($q) {
                $q->where('status', 'completed')
                  ->where('type', 'payment')
                  ->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            }])
            ->with(['settlements' => function($q) {
                $q->latest()->limit(1);
            }])
            ->with(['announcements' => function($q) {
                $q->latest()->limit(1);
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $organizations = $query->latest()->get();

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        $organizationTypes = \App\Models\OrganizationType::all();
        return view('admin.organizations.create', compact('organizationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_type_id' => 'required|exists:organization_types,id',
            'email' => 'required|email|unique:organizations,email',
            'phone' => 'nullable|string|max:20',
            'pic_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'platform_fee_operator' => 'nullable|in:and,or',
            'platform_fee_fixed' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Organization::create($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization created successfully.');
    }

    public function show(Organization $organization)
    {
        $organization->load(['members', 'charges', 'transactions', 'settlements']);

        return view('admin.organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organizations,email,' . $organization->id,
            'phone' => 'nullable|string|max:20',
            'pic_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'platform_fee_operator' => 'nullable|in:and,or',
            'platform_fee_fixed' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('logo')) {
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        if ($organization->logo) {
            Storage::disk('public')->delete($organization->logo);
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    public function exportCsv()
    {
        return Excel::download(new AdminOrganizationsExport, 'admin_organizations_' . date('Y-m-d_His') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportExcel()
    {
        return Excel::download(new AdminOrganizationsExport, 'admin_organizations_' . date('Y-m-d_His') . '.xlsx');
    }

    public function exportPdf()
    {
        $organizations = Organization::withCount('members')->get();
        $pdf = Pdf::loadView('admin.organizations.pdf', compact('organizations'));
        return $pdf->download('admin_organizations_' . date('Y-m-d_His') . '.pdf');
    }

    public function pendingBankDetails()
    {
        $organizations = Organization::where('bank_details_status', 'pending')
            ->latest('updated_at')
            ->get();

        $pendingCount = $organizations->count();

        return view('admin.bank-details.index', compact('organizations', 'pendingCount'));
    }

    public function approveBankDetails(Organization $organization)
    {
        $organization->update([
            'bank_name' => $organization->pending_bank_name,
            'bank_account_holder' => $organization->pending_bank_account_holder,
            'bank_account_number' => $organization->pending_bank_account_number,
            'bank_details_status' => 'approved',
            'pending_bank_name' => null,
            'pending_bank_account_holder' => null,
            'pending_bank_account_number' => null,
        ]);

        return redirect()->back()->with('success', 'Bank details approved successfully.');
    }

    public function rejectBankDetails(Request $request, Organization $organization)
    {
        $organization->update([
            'bank_details_status' => 'rejected',
            'pending_bank_name' => null,
            'pending_bank_account_holder' => null,
            'pending_bank_account_number' => null,
        ]);

        return redirect()->back()->with('success', 'Bank details rejected.');
    }
}
