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
        $query = Member::with('organization');

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
        $member->load('organization', 'transactions', 'charges', 'contactTickets');
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $organizations = Organization::orderBy('name')->get();
        return view('admin.members.edit', compact('member', 'organizations'));
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
}
