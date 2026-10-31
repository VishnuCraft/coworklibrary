<div class="space-y-8">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Administrator Dashboard
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Real-time telemetry, vendor activity, and space layout metrics across all properties.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-slate-900 to-indigo-900 text-white font-semibold text-xs shadow-md shadow-slate-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Manage All Users</span>
            </a>
        </div>
    </div>

    <!-- Analytics Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Vendors Card -->
        <a href="{{ route('admin.vendors') }}" class="group block p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-400 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Vendors</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalVendors }}</span>
                <span class="text-xs text-slate-400">Registered creators</span>
            </div>
            <div class="mt-2 text-[11px] text-indigo-600 font-semibold group-hover:underline flex items-center gap-1">
                <span>View all vendors</span>
                <span>&rarr;</span>
            </div>
        </a>

        <!-- Properties Card -->
        <a href="{{ route('admin.properties') }}" class="group block p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-violet-400 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Properties</span>
                <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalProperties }}</span>
                <span class="text-xs text-slate-400">Across {{ $totalFloors }} floors</span>
            </div>
            <div class="mt-2 text-[11px] text-violet-600 font-semibold group-hover:underline flex items-center gap-1">
                <span>Inspect all spaces</span>
                <span>&rarr;</span>
            </div>
        </a>

        <!-- Total Seats Card -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Bookable Seats</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalSeats }}</span>
                <span class="text-xs text-amber-600 font-medium">+{{ $totalWalkways }} walkways</span>
            </div>
            <div class="mt-2 flex items-center gap-3 text-[11px]">
                <span class="text-emerald-600 font-semibold">{{ $availableSeats }} Available</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-rose-600 font-semibold">{{ $occupiedSeats }} Occupied</span>
            </div>
        </div>

        <!-- Admin Team Card -->
        <a href="{{ route('admin.admins') }}" class="group block p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Administrators</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalAdmins }}</span>
                <span class="text-xs text-slate-400">Total accounts</span>
            </div>
            <div class="mt-2 text-[11px] text-amber-600 font-semibold group-hover:underline flex items-center gap-1">
                <span>View administrators</span>
                <span>&rarr;</span>
            </div>
        </a>
    </div>

    <!-- Data Tables Grid (Recent Vendors & Properties) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- RECENT VENDORS -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Vendors / Space Owners</h2>
                    <p class="text-xs text-slate-500">Newly registered users creating space portfolios</p>
                </div>
                <a href="{{ route('admin.vendors') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    View All &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-6">Vendor Name</th>
                            <th class="py-3 px-4">Properties</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-6 text-right">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentVendors as $vendor)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-slate-900">{{ $vendor->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono-code">{{ $vendor->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    {{ $vendor->properties_count }} properties
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $vendor->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $vendor->is_active ? 'Active' : 'Suspended' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right text-slate-500 font-mono-code text-[11px]">
                                    {{ $vendor->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    No vendors registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RECENT PROPERTIES & LAYOUTS -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recently Designed Properties</h2>
                    <p class="text-xs text-slate-500">Live seat and cinema grids designed by vendors</p>
                </div>
                <a href="{{ route('admin.properties') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    View All &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-6">Property / Owner</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Floors & Seats</th>
                            <th class="py-3 px-6 text-right">Layout</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentProperties as $prop)
                            @php
                                $seatsCount = $prop->spaces->flatMap->seats->where('is_walkway', false)->count();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-slate-900">{{ $prop->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $prop->user->name ?? 'Unknown Owner' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold capitalize bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $prop->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800">{{ $prop->top_level_spaces_count }}</span> floors &bull; 
                                    <span class="font-bold text-indigo-600">{{ $seatsCount }}</span> seats
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <a href="{{ route('spaces.builder', $prop) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-[11px] font-semibold transition-all shadow-2xs">
                                        <span>Inspect</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    No properties created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
