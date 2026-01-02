<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $organization = auth()->user()->organization;

        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Member statistics
        $totalMembers = $organization->members()->count();
        $activeMembers = $organization->members()->where('status', 'active')->count();
        $inactiveMembers = $organization->members()->where('status', 'inactive')->count();
        $newMembersInPeriod = $organization->members()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Member retention rate
        $membersLastMonth = $organization->members()
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();
        $retentionRate = $membersLastMonth > 0 ? round(($activeMembers / $membersLastMonth) * 100, 2) : 0;

        // Financial statistics
        $totalRevenue = $organization->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');

        $revenueInPeriod = $organization->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $pendingPayments = $organization->transactions()
            ->where('type', 'payment')
            ->where('status', 'pending')
            ->sum('amount');

        // Average revenue per member
        $avgRevenuePerMember = $totalMembers > 0 ? round($totalRevenue / $totalMembers, 2) : 0;

        // Revenue comparison (current period vs previous period)
        $previousPeriodStart = Carbon::parse($startDate)->subDays(Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)));
        $previousPeriodEnd = Carbon::parse($startDate)->subDay();
        $revenuePreviousPeriod = $organization->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');
        $revenueGrowth = $revenuePreviousPeriod > 0 ? round((($revenueInPeriod - $revenuePreviousPeriod) / $revenuePreviousPeriod) * 100, 2) : 0;

        // Charge statistics
        $activeCharges = $organization->charges()->where('status', 'active')->count();
        $totalCharges = $organization->charges()->count();

        // Transaction statistics
        $totalTransactions = $organization->transactions()->count();
        $completedTransactions = $organization->transactions()->where('status', 'completed')->count();
        $pendingTransactions = $organization->transactions()->where('status', 'pending')->count();

        // Monthly revenue chart data (last 6 months)
        $monthlyRevenue = $organization->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function($transaction) {
                return Carbon::parse($transaction->created_at)->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => $group->first()->created_at->format('Y-m'),
                    'total' => $group->sum('amount')
                ];
            })
            ->sortBy('month')
            ->values();

        // Member growth chart data (last 6 months)
        $memberGrowth = $organization->members()
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy(function($member) {
                return Carbon::parse($member->created_at)->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => $group->first()->created_at->format('Y-m'),
                    'total' => $group->count()
                ];
            })
            ->sortBy('month')
            ->values();

        // Top charges by revenue
        $topCharges = $organization->charges()
            ->withCount(['transactions as revenue' => function($query) use ($startDate, $endDate) {
                $query->select(DB::raw('SUM(amount)'))
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        return view('organization.reports.index', compact(
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'newMembersInPeriod',
            'retentionRate',
            'totalRevenue',
            'revenueInPeriod',
            'pendingPayments',
            'avgRevenuePerMember',
            'revenueGrowth',
            'activeCharges',
            'totalCharges',
            'totalTransactions',
            'completedTransactions',
            'pendingTransactions',
            'monthlyRevenue',
            'memberGrowth',
            'topCharges',
            'startDate',
            'endDate'
        ));
    }
}
