<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Settlement;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Key statistics
        $stats = [
            'total_organizations' => Organization::count(),
            'active_organizations' => Organization::where('status', 'active')->count(),
            'total_members' => Member::count(),
            'total_revenue' => Transaction::where('status', 'completed')->where('type', 'payment')->sum('amount'),
            'pending_settlements' => Settlement::where('status', 'pending')->count(),
            'pending_settlements_amount' => Settlement::where('status', 'pending')->sum('amount'),
        ];

        // Recent transactions by organization (last 10)
        $recent_transactions = Transaction::with(['organization', 'member'])
            ->latest()
            ->take(5)
            ->get();

        // New member signups by organization (last 30 days)
        $new_members = Member::with('organization')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->latest()
            ->take(5)
            ->get();

        // Monthly revenue chart (last 6 months)
        $monthlyRevenue = Transaction::where('type', 'payment')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function($transaction) {
                return Carbon::parse($transaction->created_at)->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => Carbon::parse($group->first()->created_at)->format('M Y'),
                    'total' => $group->sum('amount')
                ];
            })
            ->sortBy('month')
            ->values();

        // Organizations growth chart (last 6 months)
        $organizationsGrowth = Organization::where('created_at', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function($org) {
                return Carbon::parse($org->created_at)->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => Carbon::parse($group->first()->created_at)->format('M Y'),
                    'total' => $group->count()
                ];
            })
            ->sortBy('month')
            ->values();

        // Last synced timestamp to accounting system
        $lastSyncedTransaction = Transaction::whereNotNull('synced_at')
            ->latest('synced_at')
            ->first();

        $lastSyncedMember = Member::whereNotNull('accounting_sync_at')
            ->latest('accounting_sync_at')
            ->first();

        $lastSynced = null;
        if ($lastSyncedTransaction && $lastSyncedMember) {
            $lastSynced = $lastSyncedTransaction->synced_at > $lastSyncedMember->accounting_sync_at
                ? $lastSyncedTransaction->synced_at
                : $lastSyncedMember->accounting_sync_at;
        } elseif ($lastSyncedTransaction) {
            $lastSynced = $lastSyncedTransaction->synced_at;
        } elseif ($lastSyncedMember) {
            $lastSynced = $lastSyncedMember->accounting_sync_at;
        }

        // Organizations summary with member counts
        $organizations = Organization::withCount(['members', 'transactions'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_transactions',
            'new_members',
            'monthlyRevenue',
            'organizationsGrowth',
            'lastSynced',
            'organizations'
        ))->with('maintenanceMode', Setting::isMaintenanceMode());
    }
}
