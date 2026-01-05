<aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-100">
        @if(auth()->user()->role === 'super_admin')
            <div class="flex items-center justify-center">
                <img src="{{ asset('storage/logos/mymember_logo_white.png') }}" alt="MyMember Logo" class="h-12 w-auto">
            </div>
            <p class="text-xs text-gray-500 text-center mt-2">Admin Panel</p>
        @else
            <div class="flex items-center space-x-3">
                @if(auth()->user()->organization && auth()->user()->organization->logo)
                    <img src="{{ asset('storage/' . auth()->user()->organization->logo) }}" alt="{{ auth()->user()->organization->name }}" class="w-10 h-10 rounded-lg object-cover">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-bold text-gray-900">{{ auth()->user()->organization->name ?? 'Organization' }}</h1>
                    <p class="text-xs text-gray-500">Organization Portal</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4">
        @if(auth()->user()->role === 'super_admin')
            @include('layouts.partials.admin-menu')
        @else
            @include('layouts.partials.organization-menu')
        @endif
    </nav>
</aside>
