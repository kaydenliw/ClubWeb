@extends('layouts.dashboard')

@section('title', 'Organization Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('organization.members.create') }}" class="flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Add Member
            </a>
            <a href="{{ route('organization.charges.create') }}" class="flex items-center gap-2 px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Charge
            </a>
            <a href="{{ route('organization.announcements.create') }}" class="flex items-center gap-2 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                New Announcement
            </a>
            <a href="{{ route('organization.tickets.index') }}" class="flex items-center gap-2 px-4 py-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                View Tickets
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_members'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $stats['active_members'] }} active</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">RM {{ number_format($stats['total_revenue'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Completed payments</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Open Tickets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_tickets'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">Pending response</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">New Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $recent_members->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">Recently joined</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Trend (Last 6 Months)</h3>
            <div style="height: 250px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Member Growth Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Member Growth (Last 6 Months)</h3>
            <div style="height: 250px;">
                <canvas id="memberChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Recent Members -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Recent Members</h3>
                <a href="{{ route('organization.members.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="space-y-2">
                @forelse($recent_members as $member)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-semibold text-gray-600">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->car_brand }} {{ $member->car_model }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $member->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-8">No members yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Recent Transactions</h3>
                <a href="{{ route('organization.transactions.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="space-y-2">
                @forelse($recent_transactions as $txn)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-100">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $txn->member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $txn->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $txn->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($txn->amount), 2) }}
                    </p>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No transactions yet</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Announcements -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Upcoming Announcements</h3>
                <a href="{{ route('organization.announcements.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="space-y-2">
                @forelse($upcoming_announcements as $announcement)
                <div class="p-3 hover:bg-gray-50 rounded-lg transition">
                    <p class="text-sm font-medium text-gray-900">{{ $announcement->title }}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($announcement->content), 80) }}</p>
                    <div class="flex items-center mt-2 text-xs text-gray-400">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $announcement->published_at->format('M d, Y') }}
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-8">No upcoming announcements</p>
                @endforelse
            </div>
        </div>

        <!-- New Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">New Tickets</h3>
                <a href="{{ route('organization.tickets.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="space-y-2">
                @forelse($new_tickets as $ticket)
                <div class="p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">From: {{ $ticket->member->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">{{ $ticket->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-8">No open tickets</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($revenue_chart_data);
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(d => d.month),
        datasets: [{
            label: 'Revenue (RM)',
            data: revenueData.map(d => d.revenue),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Member Growth Chart
const memberCtx = document.getElementById('memberChart').getContext('2d');
const memberData = @json($member_chart_data);
const memberChart = new Chart(memberCtx, {
    type: 'line',
    data: {
        labels: memberData.map(d => d.month),
        datasets: [{
            label: 'Total Members',
            data: memberData.map(d => d.count),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush