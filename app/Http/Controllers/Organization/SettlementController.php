<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $settlements = $query->latest('settlement_date')->get();

        // Get last completed settlement
        $lastSettlement = Settlement::where('organization_id', $orgId)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        // Get next upcoming settlement
        $upcomingSettlement = Settlement::where('organization_id', $orgId)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('scheduled_date', 'asc')
            ->first();

        // Calculate stats
        $stats = [
            'last_settlement' => $lastSettlement,
            'upcoming_settlement' => $upcomingSettlement,
            'pending_settlement' => Settlement::where('organization_id', $orgId)
                ->whereIn('status', ['pending', 'processing'])
                ->sum('amount'),
            'pending_settlement_date' => $upcomingSettlement ? $upcomingSettlement->scheduled_date : null,
        ];

        // Calculate totals for filtered results
        $totals = $query->selectRaw('SUM(amount) as total_amount')->first();

        return view('organization.settlements.index', compact('settlements', 'stats', 'totals'));
    }

    public function show(Settlement $settlement)
    {
        if ($settlement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        // Load transactions with charge details
        $settlement->load(['transactions.charge']);

        // Calculate totals
        $totalAmount = $settlement->transactions->sum('amount');
        $totalPlatformFee = $settlement->transactions->sum('platform_fee');
        $netAmount = $totalAmount - $totalPlatformFee;

        return view('organization.settlements.show', compact('settlement', 'totalAmount', 'totalPlatformFee', 'netAmount'));
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
            'bank_account_holder' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        $organization = auth()->user()->organization;

        // Store pending bank details for admin approval
        $organization->update([
            'pending_bank_name' => $validated['bank_name'],
            'pending_bank_account_holder' => $validated['bank_account_holder'],
            'pending_bank_account_number' => $validated['bank_account_number'],
            'bank_details_status' => 'pending',
            'bank_details_reject_reason' => null,
        ]);

        return redirect()->route('organization.settlements.index')
            ->with('success', 'Bank details submitted for admin approval.');
    }

    public function downloadReceipt(Settlement $settlement)
    {
        if ($settlement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $settlement->load(['transactions.charge', 'organization']);

        $totalAmount = $settlement->transactions->sum('amount');
        $totalPlatformFee = $settlement->transactions->sum('platform_fee');
        $netAmount = $totalAmount - $totalPlatformFee;

        $pdf = Pdf::loadView('organization.settlements.receipt-pdf', compact('settlement', 'totalAmount', 'totalPlatformFee', 'netAmount'));

        return $pdf->stream('settlement-receipt-' . $settlement->settlement_number . '.pdf');
    }
}
