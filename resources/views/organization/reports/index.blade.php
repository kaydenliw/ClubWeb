@extends('layouts.dashboard')

@section('page-title', 'Reports & Analytics')

@section('content')
@include('layouts.partials.breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => route('organization.dashboard')],
    ['label' => 'Reports', 'url' => null]
]])
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Reports & Analytics</h3>
            <p class="text-sm text-gray-500 mt-1">View detailed statistics and insights</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('organization.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Apply Filter
            </button>
            @if(request('start_date') || request('end_date'))
            <a href="{{ route('organization.reports.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Member Retention Rate -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Retention Rate</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $retentionRate }}%</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $activeMembers }}/{{ $totalMembers }} active</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue Growth -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Revenue Growth</p>
                    <p class="text-3xl font-bold {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%</p>
                    <p class="text-xs text-gray-500 mt-2">vs previous period</p>
                </div>
                <div class="{{ $revenueGrowth >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg p-3">
                    <svg class="w-6 h-6 {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $revenueGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Revenue Per Member -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avg Revenue/Member</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">RM {{ number_format($avgRevenuePerMember, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Lifetime value</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Period Revenue -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Period Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">RM {{ number_format($revenueInPeriod, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Selected period</p>
                </div>
                <div class="bg-purple-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- Additional Statistics Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending Payments</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">RM {{ number_format($pendingPayments, 2) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Charges -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Charges</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $activeCharges }} / {{ $totalCharges }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Transaction Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Transactions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalTransactions }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $completedTransactions }} completed, {{ $pendingTransactions }} pending</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Monthly Revenue (Last 6 Months)</h3>
            <div style="height: 300px;">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        <!-- Member Growth Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Member Growth (Last 6 Months)</h3>
            <div style="height: 300px;">
                <canvas id="memberGrowthChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Revenue Chart
const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
        datasets: [{
            label: 'Revenue (RM)',
            data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
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
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Member Growth Chart
const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
const memberGrowthChart = new Chart(memberGrowthCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($memberGrowth->pluck('month')) !!},
        datasets: [{
            label: 'New Members',
            data: {!! json_encode($memberGrowth->pluck('total')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
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
