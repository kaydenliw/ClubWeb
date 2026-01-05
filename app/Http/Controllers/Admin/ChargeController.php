<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::with('organization');

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by organization
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $charges = $query->latest()->paginate(15)->withQueryString();

        // Get pending charges count for badge
        $pendingCount = Charge::where('approval_status', 'pending')->count();

        return view('admin.charges.index', compact('charges', 'pendingCount'));
    }

    public function show(Charge $charge)
    {
        $charge->load('organization', 'members');

        return view('admin.charges.show', compact('charge'));
    }

    public function approve(Charge $charge)
    {
        $charge->update([
            'approval_status' => 'approved',
            'reject_reason' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Charge approved successfully']);
    }

    public function reject(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $charge->update([
            'approval_status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
        ]);

        return redirect()->back()->with('success', 'Charge rejected successfully');
    }

    public function updateFee(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'platform_fee_operator' => 'nullable|in:and,or',
            'platform_fee_fixed' => 'nullable|numeric|min:0',
        ]);

        $charge->update($validated);

        return redirect()->back()->with('success', 'Platform fee updated successfully');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:charges,id'
        ]);

        Charge::whereIn('id', $validated['ids'])
            ->update(['approval_status' => 'approved', 'reject_reason' => null]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' charge(s) approved']);
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:charges,id',
            'reject_reason' => 'required|string|max:500'
        ]);

        Charge::whereIn('id', $validated['ids'])
            ->update(['approval_status' => 'rejected', 'reject_reason' => $validated['reject_reason']]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' charge(s) rejected']);
    }
}
