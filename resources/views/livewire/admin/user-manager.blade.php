<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                All Users Directory
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Unified overview of all registered accounts (Administrators, Vendors &amp; Customers) with real-time filtering.
            </p>
        </div>

        <div>
            <button 
                id="btn-create-user-modal"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-900 text-white font-semibold text-xs shadow-md shadow-indigo-950/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Create New User</span>
            </button>
        </div>
    </div>

    <!-- Feedback Message Banner -->
    @if($feedbackMessage)
        <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-900 flex items-center justify-between animate-in fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button wire:click="$set('feedbackMessage', null)" class="text-indigo-400 hover:text-indigo-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- FILTER & SEARCH TOOLBAR -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Search Field -->
            <div class="relative flex-1 max-w-md w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg wire:loading.remove wire:target="search" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <svg wire:loading wire:target="search" class="w-4 h-4 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="search-users-input"
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search by name or email address..."
                    class="w-full pl-10 pr-9 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition-all"
                />
                @if($search)
                    <button 
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Status Filter Select & Reset Button -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="flex items-center gap-2">
                    <label for="status-filter" class="text-xs font-semibold text-slate-500 whitespace-nowrap">
                        Status:
                    </label>
                    <select 
                        id="status-filter"
                        wire:model.live="statusFilter"
                        class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-medium focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition-all cursor-pointer">
                        <option value="all">All Statuses ({{ $allCount }})</option>
                        <option value="active">Active Only ({{ $activeCount }})</option>
                        <option value="inactive">Suspended Only ({{ $inactiveCount }})</option>
                    </select>
                </div>

                @if($search !== '' || $roleFilter !== 'all' || $statusFilter !== 'all')
                    <button 
                        id="btn-reset-filters"
                        wire:click="resetFilters" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold hover:bg-rose-100 transition-colors cursor-pointer"
                        title="Clear all active filters">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Role Filter Segmented Pills -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1 hidden sm:inline">
                    Role Filter:
                </span>

                <!-- All Roles Pill -->
                <button 
                    id="filter-role-all"
                    wire:click="setRoleFilter('all')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer {{ $roleFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 hover:text-slate-900' }}">
                    <span>All Roles</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $roleFilter === 'all' ? 'bg-slate-700 text-slate-200' : 'bg-slate-200 text-slate-600' }}">
                        {{ $allCount }}
                    </span>
                </button>

                <!-- Customers Pill -->
                <button 
                    id="filter-role-customer"
                    wire:click="setRoleFilter('customer')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer {{ $roleFilter === 'customer' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 hover:text-slate-900' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Customers</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $roleFilter === 'customer' ? 'bg-emerald-700 text-emerald-100' : 'bg-slate-200 text-slate-600' }}">
                        {{ $customerCount }}
                    </span>
                </button>

                <!-- Vendors Pill -->
                <button 
                    id="filter-role-vendor"
                    wire:click="setRoleFilter('vendor')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer {{ $roleFilter === 'vendor' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 hover:text-slate-900' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Vendors</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $roleFilter === 'vendor' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-200 text-slate-600' }}">
                        {{ $vendorCount }}
                    </span>
                </button>

                <!-- Administrators Pill -->
                <button 
                    id="filter-role-admin"
                    wire:click="setRoleFilter('administrator')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer {{ $roleFilter === 'administrator' ? 'bg-violet-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 hover:text-slate-900' }}">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Administrators</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $roleFilter === 'administrator' ? 'bg-violet-800 text-violet-200' : 'bg-slate-200 text-slate-600' }}">
                        {{ $adminCount }}
                    </span>
                </button>
            </div>

            <div class="text-[11px] text-slate-400 font-medium">
                <span wire:loading.remove wire:target="search, roleFilter, statusFilter, setRoleFilter, setStatusFilter, resetFilters">
                    Showing <strong class="text-slate-700">{{ $users->count() }}</strong> results
                </span>
                <span wire:loading wire:target="search, roleFilter, statusFilter, setRoleFilter, setStatusFilter, resetFilters" class="inline-flex items-center gap-1.5 text-indigo-600 font-semibold animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-ping"></span>
                    <span>Filtering users...</span>
                </span>
            </div>
        </div>
    </div>

    <!-- USERS DIRECTORY TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <!-- Slim Loading Progress Bar -->
        <div wire:loading wire:target="search, roleFilter, statusFilter, setRoleFilter, setStatusFilter, resetFilters" class="h-0.5 w-full bg-slate-100 overflow-hidden">
            <div class="h-full bg-indigo-600 animate-pulse w-full"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3.5 px-6">User / Account</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Spaces / Activity</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Registered Date</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="search, roleFilter, statusFilter, setRoleFilter, setStatusFilter, resetFilters" class="divide-y divide-slate-100 transition-opacity duration-150">
                    @forelse($users as $u)
                        @php
                            $isAdmin = $u->isAdministrator();
                            $isCustomer = ($u->role === 'customer');
                            $isVendor = ($u->role === 'vendor');
                            $totalSeats = $u->properties ? $u->properties->flatMap->spaces->flatMap->seats->where('is_walkway', false)->count() : 0;
                            $totalWalkways = $u->properties ? $u->properties->flatMap->spaces->flatMap->seats->where('is_walkway', true)->count() : 0;
                            $propertyCount = $u->properties_count ?? ($u->properties ? $u->properties->count() : 0);
                            $bookingCount = $u->bookings ? $u->bookings->count() : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- User Identity -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $isAdmin ? 'bg-violet-100 text-violet-700 border border-violet-200' : ($isCustomer ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-indigo-100 text-indigo-700 border border-indigo-200') }} flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 truncate">
                                                {{ $u->name }}
                                            </span>
                                            @if($u->id === auth()->id())
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider bg-slate-900 text-amber-300">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <span class="block text-slate-500 font-mono-code text-[11px] truncate">
                                            {{ $u->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($isAdmin)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-violet-50 text-violet-700 border border-violet-200">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <span>Administrator</span>
                                    </span>
                                @elseif($isCustomer)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Customer</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Vendor</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Activity / Spaces Overview -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($isAdmin)
                                    <span class="text-slate-400 font-medium text-[11px] italic">
                                        Central Panel Administrator
                                    </span>
                                @elseif($isCustomer)
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px] border border-emerald-200">
                                            {{ $bookingCount }} {{ \Illuminate\Support\Str::plural('Booking', $bookingCount) }}
                                        </span>
                                        <span class="text-slate-400 text-[11px]">
                                            Student / Customer
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-800">
                                            {{ $propertyCount }} {{ \Illuminate\Support\Str::plural('Property', $propertyCount) }}
                                        </span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-slate-500 text-[11px]">
                                            {{ $totalSeats }} seats ({{ $totalWalkways }} walkways)
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Active</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Suspended</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Registered Date -->
                            <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-mono-code text-[11px]">
                                {{ $u->created_at ? $u->created_at->format('d M, Y') : 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <!-- Switch To User Button -->
                                    @if($u->id !== auth()->id() && $u->is_active)
                                        <button 
                                            wire:click="switchToUser({{ $u->id }})"
                                            wire:confirm="Do you want to switch and login as '{{ $u->name }}'?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50/80 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 text-[11px] font-semibold transition-all cursor-pointer shadow-2xs"
                                            title="Login as {{ $u->name }}">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <span class="hidden sm:inline">Switch</span>
                                        </button>
                                    @endif

                                    <!-- Toggle Status Button -->
                                    @if($u->id !== auth()->id())
                                        <button 
                                            wire:click="toggleStatus({{ $u->id }})"
                                            class="p-1.5 rounded-lg border text-xs font-semibold transition-all cursor-pointer {{ $u->is_active ? 'border-slate-200 text-slate-600 hover:text-amber-700 hover:bg-amber-50 hover:border-amber-200' : 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}"
                                            title="{{ $u->is_active ? 'Suspend Account' : 'Re-activate Account' }}">
                                            @if($u->is_active)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    @if($u->id !== auth()->id())
                                        <button 
                                            wire:click="deleteUser({{ $u->id }})"
                                            wire:confirm="Are you sure you want to delete '{{ $u->name }}'? This will permanently delete their account and associated properties."
                                            class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-all cursor-pointer"
                                            title="Delete User Account">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">No users match your filters</p>
                                    <p class="text-xs text-slate-400">
                                        Try adjusting your search query, role filter, or status filter.
                                    </p>
                                    @if($search !== '' || $roleFilter !== 'all' || $statusFilter !== 'all')
                                        <button 
                                            wire:click="resetFilters" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs hover:bg-indigo-100 transition-colors cursor-pointer">
                                            <span>Clear all filters</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE USER MODAL -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Create New User Account</h3>
                        <p class="text-xs text-slate-500">Create an Administrator or Vendor account directly from this panel.</p>
                    </div>
                    <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form wire:submit="createUser" class="space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                        <input 
                            type="text" 
                            wire:model="name"
                            placeholder="e.g. John Doe or Cowork Library Manager"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15"
                        />
                        @error('name') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            wire:model="email"
                            placeholder="user@example.com"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15"
                        />
                        @error('email') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- User Role Selection Cards -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select User Role</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <!-- Customer Card -->
                            <label class="relative flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all {{ $role === 'customer' ? 'border-emerald-600 bg-emerald-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900">Customer</span>
                                    <input type="radio" wire:model="role" value="customer" class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <span class="text-[10px] text-slate-500 leading-tight">
                                    Student/Booker looking for co-working or library space.
                                </span>
                            </label>

                            <!-- Vendor Card -->
                            <label class="relative flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all {{ $role === 'vendor' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900">Vendor</span>
                                    <input type="radio" wire:model="role" value="vendor" class="text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <span class="text-[10px] text-slate-500 leading-tight">
                                    Property creator &amp; floor layout manager.
                                </span>
                            </label>

                            <!-- Administrator Card -->
                            <label class="relative flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all {{ $role === 'administrator' ? 'border-violet-600 bg-violet-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900">Administrator</span>
                                    <input type="radio" wire:model="role" value="administrator" class="text-violet-600 focus:ring-violet-500">
                                </div>
                                <span class="text-[10px] text-slate-500 leading-tight">
                                    Full access to manage the entire platform.
                                </span>
                            </label>
                        </div>
                        @error('role') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                            <input 
                                type="password" 
                                wire:model="password"
                                placeholder="Min 6 characters"
                                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15"
                            />
                            @error('password') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                            <input 
                                type="password" 
                                wire:model="password_confirmation"
                                placeholder="Repeat password"
                                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15"
                            />
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closeCreateModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all flex items-center gap-1.5 cursor-pointer">
                            <span wire:loading.remove>Create Account</span>
                            <span wire:loading>Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
