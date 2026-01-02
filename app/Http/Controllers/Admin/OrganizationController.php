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
        $query = Organization::withCount(['members', 'transactions', 'charges']);

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
        return view('admin.organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organizations,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
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
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
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
}
