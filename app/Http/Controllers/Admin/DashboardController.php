<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Settlement;
use App\Models\Charge;
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
            'total_transactions_this_month' => Transaction::where('status', 'completed')
                ->where('type', 'payment')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount'),
            'pending_settlements' => Settlement::where('status', 'pending')->count(),
            'pending_settlements_amount' => Settlement::where('status', 'pending')->sum('amount'),
            'pending_settlements_orgs' => Settlement::where('status', 'pending')
                ->distinct('organization_id')
                ->count('organization_id'),
            'pending_charges' => Charge::where('approval_status', 'pending')->count(),
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

        // Monthly Total Transactions chart (Aug 2025 to Jan 2026)
        $startDate = Carbon::create(2025, 8, 1);
        $endDate = Carbon::create(2026, 1, 31);

        $monthlyTransactions = Transaction::where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($transaction) {
                return Carbon::parse($transaction->created_at)->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => Carbon::parse($group->first()->created_at)->format('M y'),
                    'total' => $group->sum('amount')
                ];
            });

        // Fill in missing months with zero values
        $allMonths = collect();
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            $allMonths[$key] = [
                'month' => $current->format('M y'),
                'total' => $monthlyTransactions->get($key)['total'] ?? 0
            ];
            $current->addMonth();
        }
        $monthlyTransactions = $allMonths->values();

        // Monthly Total Profit chart (last 6 months)
        $monthlyProfit = Transaction::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function($transaction) {
                return Carbon::parse($transaction->created_at)->format('Y-m');
            })
            ->map(function($group) {
                $payments = $group->where('type', 'payment')->sum('amount');
                $refunds = $group->where('type', 'refund')->sum('amount');
                return [
                    'month' => Carbon::parse($group->first()->created_at)->format('M y'),
                    'total' => $payments - $refunds
                ];
            })
            ->sortBy('month')
            ->values();

        // Top 5 Organizations (Last month) with transaction amounts and member counts
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $topOrganizations = Organization::withCount('members')
            ->with(['transactions' => function($query) use ($lastMonthStart, $lastMonthEnd) {
                $query->where('status', 'completed')
                    ->where('type', 'payment')
                    ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd]);
            }])
            ->get()
            ->map(function($org) {
                return [
                    'name' => $org->name,
                    'total_amount' => $org->transactions->sum('amount'),
                    'members_count' => $org->members_count
                ];
            })
            ->sortByDesc('total_amount')
            ->take(5)
            ->values();

        // Organizations with ZERO transactions last month
        $orgsWithZeroTransactions = Organization::whereDoesntHave('transactions', function($query) use ($lastMonthStart, $lastMonthEnd) {
                $query->where('status', 'completed')
                    ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd]);
            })
            ->count();

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
            'monthlyTransactions',
            'monthlyProfit',
            'topOrganizations',
            'orgsWithZeroTransactions',
            'lastSynced',
            'organizations'
        ))->with('maintenanceMode', Setting::isMaintenanceMode());
    }
}
