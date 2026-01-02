<header class="bg-white border-b border-gray-200">
    <div class="flex items-center justify-between px-8 py-4">
        <!-- Left Section: Page Title & Breadcrumb -->
        <div class="flex items-center flex-1">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    @yield('page-title', 'Dashboard')
                </h2>
                @if(auth()->user()->role !== 'super_admin')
                    <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->organization->name ?? 'Organization' }}</p>
                @endif
            </div>
        </div>

        <!-- Right Section: Actions & User -->
        <div class="flex items-center space-x-3">
            <!-- Notifications -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 pl-3 border-l border-gray-200 hover:bg-gray-50 rounded-lg py-2 pr-2 transition">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        @if(auth()->user()->role === 'super_admin')
                            <p class="text-xs text-gray-500">Super Admin</p>
                        @else
                            <p class="text-xs text-gray-500">Organization Admin</p>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                        <span class="text-sm font-semibold text-gray-600">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">

                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->role !== 'super_admin')
                            <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->organization->name ?? 'Organization' }}</p>
                        @endif
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        <a href="{{ route('organization.profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
