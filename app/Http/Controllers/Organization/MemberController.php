<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Exports\MembersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('car_plate', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->withCount('charges')->latest()->paginate(15);
        return view('organization.members.index', compact('members'));
    }

    public function create()
    {
        return view('organization.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'car_brand' => 'nullable|string|max:100',
            'car_model' => 'nullable|string|max:100',
            'car_plate' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;
        Member::create($validated);

        return redirect()->route('organization.members.index')
            ->with('success', 'Member added successfully.');
    }

    public function show(Member $member)
    {
        if ($member->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $member->load(['charges', 'transactions']);
        return view('organization.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        if ($member->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        if ($member->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'car_brand' => 'nullable|string|max:100',
            'car_model' => 'nullable|string|max:100',
            'car_plate' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $member->update($validated);

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
        $members = Member::where('organization_id', auth()->user()->organization_id)
            ->withCount('charges')
            ->get();

        $filename = 'members_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($members) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Name', 'Email', 'Phone', 'Car Brand', 'Car Model', 'Car Plate', 'Status', 'Charges Count', 'Joined Date']);

            // Add data rows
            foreach ($members as $member) {
                fputcsv($file, [
                    $member->name,
                    $member->email,
                    $member->phone ?? '',
                    $member->car_brand ?? '',
                    $member->car_model ?? '',
                    $member->car_plate ?? '',
                    $member->status,
                    $member->charges_count,
                    $member->created_at->format('Y-m-d H:i:s'),
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
}
