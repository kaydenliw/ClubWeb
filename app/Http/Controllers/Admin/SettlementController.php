<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Models\Organization;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $query = Settlement::with('organization');

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('settlement_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('settlement_date', '<=', $request->end_date);
        }

        $settlements = $query->latest()->get();
        $organizations = Organization::orderBy('name')->get();

        return view('admin.settlements.index', compact('settlements', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('name')->get();
        return view('admin.settlements.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'amount' => 'required|numeric|min:0',
            'settlement_date' => 'required|date',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string'
        ]);

        $validated['settlement_number'] = 'STL' . strtoupper(uniqid());

        Settlement::create($validated);

        return redirect()->route('admin.settlements.index')
            ->with('success', 'Settlement created successfully.');
    }

    public function show(Settlement $settlement)
    {
        $settlement->load('organization');
        return view('admin.settlements.show', compact('settlement'));
    }

    public function edit(Settlement $settlement)
    {
        $organizations = Organization::orderBy('name')->get();
        return view('admin.settlements.edit', compact('settlement', 'organizations'));
    }

    public function update(Request $request, Settlement $settlement)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'amount' => 'required|numeric|min:0',
            'settlement_date' => 'required|date',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string'
        ]);

        $settlement->update($validated);

        return redirect()->route('admin.settlements.show', $settlement)
            ->with('success', 'Settlement updated successfully.');
    }

    public function destroy(Settlement $settlement)
    {
        try {
            $settlement->delete();
            return redirect()->route('admin.settlements.index')
                ->with('success', 'Settlement deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settlements.index')
                ->with('error', 'Failed to delete settlement. ' . $e->getMessage());
        }
    }

    public function approve(Settlement $settlement)
    {
        $settlement->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'reject_reason' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Settlement approved successfully']);
    }

    public function reject(Request $request, Settlement $settlement)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        $settlement->update([
            'approval_status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'approved_at' => null,
            'approved_by' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Settlement rejected successfully']);
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:settlements,id'
        ]);

        Settlement::whereIn('id', $validated['ids'])->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'reject_reason' => null
        ]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' settlement(s) approved']);
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:settlements,id',
            'reject_reason' => 'required|string|max:500'
        ]);

        Settlement::whereIn('id', $validated['ids'])->update([
            'approval_status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'approved_at' => null,
            'approved_by' => null
        ]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' settlement(s) rejected']);
    }
}
