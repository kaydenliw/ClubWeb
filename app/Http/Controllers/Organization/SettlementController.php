<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $query = Settlement::where('organization_id', $orgId);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('settlement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('settlement_date', '<=', $request->date_to);
        }

        $settlements = $query->latest('settlement_date')->paginate(15)->withQueryString();

        // Calculate stats
        $stats = [
            'total_settled' => Settlement::where('organization_id', $orgId)
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_settlement' => Settlement::where('organization_id', $orgId)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_settlements' => Settlement::where('organization_id', $orgId)->count(),
        ];

        return view('organization.settlements.index', compact('settlements', 'stats'));
    }

    public function show(Settlement $settlement)
    {
        if ($settlement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.settlements.show', compact('settlement'));
    }

    public function editBankDetails()
    {
        $organization = auth()->user()->organization;
        return view('organization.settlements.edit-bank', compact('organization'));
    }

    public function updateBankDetails(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        $organization = auth()->user()->organization;
        $organization->update($validated);

        return redirect()->route('organization.settlements.index')
            ->with('success', 'Bank details updated successfully.');
    }
}
