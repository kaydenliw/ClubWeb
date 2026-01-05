@extends('layouts.dashboard')

@section('title', 'Organization Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Upcoming Announcements and New Tickets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Upcoming Announcements -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Upcoming Announcements</h3>
                <a href="{{ route('organization.announcements.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table id="announcementsTable" class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Title</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sample_announcements as $announcement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($announcement->title, 30) }}</td>
                            <td class="px-3 py-2">
                                @if($announcement->approval_status === 'pending_approval')
                                <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif(in_array($announcement->approval_status, ['approved_pending_publish', 'approved_published']))
                                <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700">Approved</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">{{ ucfirst($announcement->approval_status) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $announcement->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No announcements yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">New Tickets</h3>
                <a href="{{ route('organization.tickets.index') }}" class="text-xs text-gray-600 hover:text-gray-900">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table id="ticketsTable" class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Subject</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">From</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($new_tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($ticket->subject, 30) }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $ticket->member->name }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No open tickets</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">New Members This Month</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['new_members_this_month'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ now()->format('F Y') }}</p>
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
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Transactions This Month</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['transactions_this_month'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ now()->format('F Y') }}</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
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
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Settlement</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">RM {{ number_format($stats['last_settlement_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        @if($stats['last_settlement_date'])
                            {{ $stats['last_settlement_date']->format('M d, Y h:i A') }}
                        @else
                            No settlements yet
                        @endif
                    </p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Pie Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Last Month Transaction By Charges/Plan</h3>
            <div style="height: 300px;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <!-- Combined Transaction & Members Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Transaction & Members Trend (Last 6 Months)</h3>
            <div style="height: 300px;">
                <canvas id="combinedChart"></canvas>
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
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Combined Transaction & Members Trend Chart
const combinedCtx = document.getElementById('combinedChart').getContext('2d');
const combinedData = @json($combined_chart_data);
const combinedChart = new Chart(combinedCtx, {
    type: 'bar',
    data: {
        labels: combinedData.map(d => d.month),
        datasets: [
            {
                label: 'Transaction (RM)',
                data: combinedData.map(d => d.transaction),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                yAxisID: 'y',
                order: 2
            },
            {
                label: 'No. Of Member',
                data: combinedData.map(d => d.members),
                borderColor: 'rgb(249, 115, 22)',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                type: 'line',
                tension: 0.4,
                fill: false,
                yAxisID: 'y1',
                order: 1,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(249, 115, 22)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Transaction (RM)'
                },
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toFixed(0);
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'No. Of Member'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Pie Chart - Last Month Transaction By Charges/Plan
const pieCtx = document.getElementById('pieChart').getContext('2d');
const chargeData = @json($charge_transactions);
const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(chargeData),
        datasets: [{
            data: Object.values(chargeData),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(249, 115, 22, 0.8)',   // Orange
                'rgba(156, 163, 175, 0.8)',  // Gray
                'rgba(234, 179, 8, 0.8)',    // Yellow
                'rgba(34, 197, 94, 0.8)',    // Green
                'rgba(168, 85, 247, 0.8)',   // Purple
            ],
            borderColor: [
                'rgb(59, 130, 246)',
                'rgb(249, 115, 22)',
                'rgb(156, 163, 175)',
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)',
                'rgb(168, 85, 247)',
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Initialize DataTables for announcements and tickets
$(document).ready(function() {
    @if($sample_announcements->count() > 0)
    $('#announcementsTable').DataTable({
        "pageLength": 3,
        "lengthChange": false,
        "searching": false,
        "info": false,
        "ordering": false,
        "columns": [
            null, // Title
            null, // Status
            null  // Date
        ]
    });
    @endif

    @if($new_tickets->count() > 0)
    $('#ticketsTable').DataTable({
        "pageLength": 3,
        "lengthChange": false,
        "searching": false,
        "info": false,
        "ordering": false,
        "columns": [
            null, // Subject
            null, // From
            null  // Status
        ]
    });
    @endif
});
</script>
@endpush