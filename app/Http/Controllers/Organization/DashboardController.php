<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Charge;
use App\Models\ContactTicket;
use App\Models\Announcement;
use App\Models\Settlement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;

        // Get last settlement
        $last_settlement = Settlement::where('organization_id', $orgId)
            ->latest()
            ->first();

        $stats = [
            'new_members_this_month' => Member::where('organization_id', $orgId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'transactions_this_month' => Transaction::where('organization_id', $orgId)
                ->where('status', 'completed')
                ->where('type', 'payment')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'pending_tickets' => ContactTicket::where('organization_id', $orgId)->where('status', 'open')->count(),
            'last_settlement_amount' => $last_settlement ? $last_settlement->amount : 0,
            'last_settlement_date' => $last_settlement ? $last_settlement->created_at : null,
        ];

        $recent_members = Member::where('organization_id', $orgId)->latest()->take(5)->get();
        $recent_transactions = Transaction::with('member')->where('organization_id', $orgId)->latest()->take(5)->get();

        // Get all announcements for dashboard (DataTable will handle pagination)
        $sample_announcements = Announcement::where('organization_id', $orgId)
            ->latest()
            ->get();

        // Get all open tickets for dashboard (DataTable will handle pagination)
        $new_tickets = ContactTicket::with('member')->where('organization_id', $orgId)
            ->where('status', 'open')
            ->latest()
            ->get();

        // Combined Transaction & Members Trend data (last 6 months)
        $combined_chart_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $transaction_amount = Transaction::where('organization_id', $orgId)
                ->where('status', 'completed')
                ->where('type', 'payment')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $member_count = Member::where('organization_id', $orgId)
                ->whereYear('created_at', '<=', $month->year)
                ->where(function($q) use ($month) {
                    $q->whereYear('created_at', '<', $month->year)
                      ->orWhere(function($q2) use ($month) {
                          $q2->whereYear('created_at', $month->year)
                             ->whereMonth('created_at', '<=', $month->month);
                      });
                })
                ->count();
            $combined_chart_data[] = [
                'month' => $month->format('M-y'),
                'transaction' => $transaction_amount,
                'members' => $member_count
            ];
        }

        // Last Month Transaction By Charges/Plan (by number of transactions)
        $lastMonth = now()->subMonth();
        $charge_transactions = Transaction::where('organization_id', $orgId)
            ->where('status', 'completed')
            ->where('type', 'payment')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->whereNotNull('charge_id')
            ->with('charge')
            ->get()
            ->groupBy(function($transaction) {
                return $transaction->charge ? $transaction->charge->title : 'Other';
            })
            ->map(function($group) {
                return $group->count();
            });

        return view('organization.dashboard', compact('stats', 'recent_members', 'recent_transactions', 'sample_announcements', 'new_tickets', 'combined_chart_data', 'charge_transactions'));
    }
}
