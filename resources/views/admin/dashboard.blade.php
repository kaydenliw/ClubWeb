@extends('layouts.dashboard')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with Sync Status -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Admin Dashboard</h3>
            <p class="text-sm text-gray-500 mt-1">Overview of all organizations and activities</p>
        </div>
        @if($lastSynced)
        <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
            <p class="text-xs text-green-600 font-medium">Last Accounting sync since:</p>
            <p class="text-sm font-semibold text-green-900">{{ $lastSynced->format('d M Y, h:i A') }}</p>
        </div>
        @endif
    </div>

    <!-- Maintenance Mode Toggle -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6" x-data="{
        enabled: {{ $maintenanceMode ? 'true' : 'false' }},
        toggle() {
            this.enabled = !this.enabled;
            this.$nextTick(() => {
                document.getElementById('maintenanceForm').submit();
            });
        }
    }">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-yellow-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Maintenance Mode</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        <span x-show="enabled" class="text-yellow-600 font-medium">Active - Users cannot access the portal</span>
                        <span x-show="!enabled" class="text-green-600 font-medium">Inactive - Portal is accessible</span>
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.toggle') }}" id="maintenanceForm">
                @csrf
                <input type="hidden" name="enabled" :value="enabled ? '1' : '0'">
                <button type="button"
                        @click="toggle()"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :class="enabled ? 'bg-yellow-600' : 'bg-gray-200'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                          :class="enabled ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <!-- Organizations Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organizations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_organizations'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $stats['active_organizations'] }} active</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Members Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Members</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_members'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">Across all organization</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Transactions This Month Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Transactions This Month</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">RM {{ number_format($stats['total_transactions_this_month'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Completed payments</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Settlements Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending Settlements</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_settlements'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">RM {{ number_format($stats['pending_settlements_amount'], 2) }} <span class="text-gray-400">• {{ $stats['pending_settlements_orgs'] }} organizations</span></p>
                </div>
                <div class="bg-orange-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Charges Approval Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending Charges Approval</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_charges'] }}</p>
                    <a href="{{ route('admin.charges.index', ['approval_status' => 'pending']) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium mt-2 inline-block">Review →</a>
                </div>
                <div class="bg-yellow-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Organizations with ZERO transaction last month Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organizations with ZERO transaction last month</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $orgsWithZeroTransactions }}</p>
                    <a href="{{ route('admin.organizations.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium mt-2 inline-block">View →</a>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Total Transactions Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Monthly Total Transactions</h3>
            <div style="height: 300px;">
                <canvas id="monthlyTransactionsChart"></canvas>
            </div>
        </div>

        <!-- Monthly Total Profit Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Monthly Total Profit (Last 6 Months)</h3>
            <div style="height: 300px;">
                <canvas id="monthlyProfitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Organizations Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Top 5 Organization (Last month)</h3>
        <div style="height: 350px;">
            <canvas id="topOrganizationsChart"></canvas>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Organizations List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Organizations</h3>
                <a href="{{ route('admin.organizations.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all →</a>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($organizations as $org)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($org->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($org->name, 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $org->members_count }} members</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $org->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} flex-shrink-0">
                            {{ ucfirst($org->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- New Members -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">New Members (30 Days)</h3>
                <a href="{{ route('admin.members.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all →</a>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @forelse($new_members as $member)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($member->name, 18) }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Str::limit($member->organization->name, 18) }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 flex-shrink-0">{{ $member->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No new members</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Transactions</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all →</a>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @forelse($recent_transactions as $txn)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $txn->type == 'payment' ? 'bg-green-100' : 'bg-red-100' }} flex-shrink-0">
                                <svg class="w-5 h-5 {{ $txn->type == 'payment' ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $txn->type == 'payment' ? 'M12 4v16m8-8H4' : 'M20 12H4' }}"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($txn->member->name, 15) }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Str::limit($txn->organization->name, 15) }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold {{ $txn->type == 'payment' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $txn->type == 'payment' ? '+' : '-' }}RM {{ number_format(abs($txn->amount), 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $txn->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No transactions</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Total Transactions Chart
const monthlyTransactionsCtx = document.getElementById('monthlyTransactionsChart').getContext('2d');
new Chart(monthlyTransactionsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyTransactions->pluck('month')) !!},
        datasets: [{
            label: 'Transactions (RM)',
            data: {!! json_encode($monthlyTransactions->pluck('total')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Monthly Total Profit Chart
const monthlyProfitCtx = document.getElementById('monthlyProfitChart').getContext('2d');
new Chart(monthlyProfitCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyProfit->pluck('month')) !!},
        datasets: [{
            label: 'Profit (RM)',
            data: {!! json_encode($monthlyProfit->pluck('total')) !!},
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Top 5 Organizations Chart with dual y-axis
const topOrgsCtx = document.getElementById('topOrganizationsChart').getContext('2d');
new Chart(topOrgsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($topOrganizations->pluck('name')) !!},
        datasets: [{
            label: 'Transaction Amount (RM)',
            data: {!! json_encode($topOrganizations->pluck('total_amount')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
            yAxisID: 'y'
        }, {
            label: '# of Members',
            data: {!! json_encode($topOrganizations->pluck('members_count')) !!},
            backgroundColor: 'rgba(168, 85, 247, 0.8)',
            borderColor: 'rgb(168, 85, 247)',
            borderWidth: 1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
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
                    text: 'Transaction Amount (RM)'
                },
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
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
                    text: '# of Members'
                },
                grid: {
                    drawOnChartArea: false
                },
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
