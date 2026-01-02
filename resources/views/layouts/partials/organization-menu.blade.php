<!-- Dashboard -->
<div class="mb-6">
    <a href="{{ route('organization.dashboard') }}"
       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
</div>

<!-- Member Management -->
<div class="mb-6">
    <h3 class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Member Management</h3>
    <ul class="space-y-1">
        <li>
            <a href="{{ route('organization.members.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.members.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Members
            </a>
        </li>
    </ul>
</div>

<!-- Financial -->
<div class="mb-6">
    <h3 class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Financial</h3>
    <ul class="space-y-1">
        <li>
            <a href="{{ route('organization.charges.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.charges.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Charges
            </a>
        </li>
        <li>
            <a href="{{ route('organization.transactions.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.transactions.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Transactions
            </a>
        </li>
        <li>
            <a href="{{ route('organization.settlements.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.settlements.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Settlements
            </a>
        </li>
    </ul>
</div>

<!-- Reports & Analytics -->
<div class="mb-6">
    <h3 class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports & Analytics</h3>
    <ul class="space-y-1">
        <li>
            <a href="{{ route('organization.reports.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.reports.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Reports
            </a>
        </li>
        <li>
            <a href="{{ route('organization.activity-logs.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.activity-logs.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Activity Logs
            </a>
        </li>
    </ul>
</div>

<!-- Communication & Support -->
<div class="mb-6">
    <h3 class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication & Support</h3>
    <ul class="space-y-1">
        <li>
            <a href="{{ route('organization.announcements.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.announcements.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                Announcements
            </a>
        </li>
        <li>
            <a href="{{ route('organization.faqs.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.faqs.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                FAQs
            </a>
        </li>
        <li>
            <a href="{{ route('organization.tickets.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('organization.tickets.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                Support Tickets
            </a>
        </li>
    </ul>
</div>
