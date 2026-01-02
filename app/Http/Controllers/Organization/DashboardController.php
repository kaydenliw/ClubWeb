<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Charge;
use App\Models\ContactTicket;
use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;

        $stats = [
            'total_members' => Member::where('organization_id', $orgId)->count(),
            'active_members' => Member::where('organization_id', $orgId)->where('status', 'active')->count(),
            'total_revenue' => Transaction::where('organization_id', $orgId)->where('status', 'completed')->where('type', 'payment')->sum('amount'),
            'pending_tickets' => ContactTicket::where('organization_id', $orgId)->where('status', 'open')->count(),
            'new_members_this_month' => Member::where('organization_id', $orgId)->whereMonth('created_at', now()->month)->count(),
            'total_charges' => Charge::where('organization_id', $orgId)->count(),
        ];

        $recent_members = Member::where('organization_id', $orgId)->latest()->take(5)->get();
        $recent_transactions = Transaction::with('member')->where('organization_id', $orgId)->latest()->take(5)->get();
        $upcoming_announcements = Announcement::where('organization_id', $orgId)
            ->where('is_published', true)
            ->where('published_at', '>=', now())
            ->orderBy('published_at')
            ->take(5)
            ->get();
        $new_tickets = ContactTicket::with('member')->where('organization_id', $orgId)
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        // Revenue trend data (last 6 months)
        $revenue_chart_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Transaction::where('organization_id', $orgId)
                ->where('status', 'completed')
                ->where('type', 'payment')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $revenue_chart_data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Member growth data (last 6 months)
        $member_chart_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Member::where('organization_id', $orgId)
                ->whereYear('created_at', '<=', $month->year)
                ->where(function($q) use ($month) {
                    $q->whereYear('created_at', '<', $month->year)
                      ->orWhere(function($q2) use ($month) {
                          $q2->whereYear('created_at', $month->year)
                             ->whereMonth('created_at', '<=', $month->month);
                      });
                })
                ->count();
            $member_chart_data[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        return view('organization.dashboard', compact('stats', 'recent_members', 'recent_transactions', 'upcoming_announcements', 'new_tickets', 'revenue_chart_data', 'member_chart_data'));
    }
}
