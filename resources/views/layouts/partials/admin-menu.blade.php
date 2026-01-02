<!-- Dashboard -->
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
</div>

<!-- Organization Management -->
<div class="mb-6">
    <h3 class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Organization Management</h3>
    <ul class="space-y-1">
        <li>
            <a href="{{ route('admin.organizations.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.organizations.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Organizations
            </a>
        </li>
        <li>
            <a href="{{ route('admin.members.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.members.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
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
            <a href="{{ route('admin.transactions.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Transactions
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settlements.index') }}"
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.settlements.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Settlements
            </a>
        </li>
    </ul>
</div>
