<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 text-slate-800">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Owner Portal - CoworkLibrary' }}</title>

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            }
            .font-mono-code {
                font-family: 'JetBrains Mono', monospace;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full flex flex-col md:flex-row bg-slate-100 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white" x-data="{ mobileNavOpen: false }">
        <!-- Top Global Livewire Progress Bar -->
        <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50 h-0.5 bg-indigo-600 pointer-events-none transition-all"></div>

        <!-- OWNER SIDEBAR -->
        <aside 
            :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between transition-transform duration-200 ease-in-out md:static md:translate-x-0 shrink-0">
            
            <div>
                <!-- Owner Brand Header -->
                <div class="h-16 px-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 p-0.5 shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center text-indigo-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-extrabold tracking-tight text-white flex items-center gap-1.5">
                                Cowork<span class="text-indigo-400">Library</span>
                            </span>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-400">
                                Owner Portal
                            </span>
                        </div>
                    </a>

                    <!-- Mobile close button -->
                    <button @click="mobileNavOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="px-4 py-6 space-y-1">
                    <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Workspace
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('owner.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('owner.dashboard') || request()->routeIs('vendor.dashboard') || request()->is('owner') || request()->is('owner/dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Booking Requests -->
                    <a href="{{ route('vendor.bookings') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('vendor.bookings') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Booking Requests</span>
                        </div>
                        @php
                            $pendingReqCount = \App\Models\Booking::whereHas('property', fn($q) => $q->where('user_id', auth()->id()))->where('status', 'pending')->count();
                        @endphp
                        @if($pendingReqCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 animate-pulse">
                                {{ $pendingReqCount }}
                            </span>
                        @endif
                    </a>

                    <!-- My Properties -->
                    <a href="{{ route('properties.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('properties.index') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>My Properties</span>
                    </a>

                    <!-- Visual Grid Builder (Highlighted when inside a property canvas) -->
                    @if(request()->routeIs('spaces.builder'))
                        <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-indigo-600 text-white shadow-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                            <span>Visual Grid Builder</span>
                        </div>
                    @endif

                    <!-- Seat Inventory -->
                    <a href="{{ route('vendor.seats') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('vendor.seats') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Seat Inventory</span>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Account &amp; Settings
                    </div>

                    <!-- Profile -->
                    <a href="{{ route('vendor.profile') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('vendor.profile') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Owner Profile</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Super Powers
                        </div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-amber-400 hover:text-white hover:bg-slate-800/60 transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Open Admin Panel &rarr;</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Logged In Vendor Profile & Sign Out Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ substr(auth()->user()->name ?? 'V', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-semibold text-white truncate">
                                {{ auth()->user()->name ?? 'Vendor' }}
                            </span>
                            <span class="block text-[10px] text-emerald-400 capitalize">
                                Space Owner
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors cursor-pointer"
                                title="Sign out">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Impersonation Banner (Active when Administrator switches into Vendor) -->
            @if(session()->has('impersonator_id'))
                <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-indigo-600 text-white px-4 sm:px-6 lg:px-8 py-2.5 text-xs font-semibold shadow-xs border-b border-amber-400/30 shrink-0">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white font-extrabold text-[10px] uppercase tracking-wider shrink-0">
                                Switched User Mode
                            </span>
                            <span class="truncate">
                                Currently operating as Vendor <strong>{{ auth()->user()->name }}</strong> (<span class="font-mono-code">{{ auth()->user()->email }}</span>).
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.switch-back') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white text-slate-900 font-bold hover:bg-amber-50 transition-colors shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Return to Administrator Panel &rarr;</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Vendor Topbar -->
            <header class="h-16 bg-white border-b border-slate-200/90 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-3">
                    <button @click="mobileNavOpen = true" class="md:hidden text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-500 hidden sm:inline-block">
                            CoworkLibrary
                        </span>
                        <span class="text-slate-300 hidden sm:inline-block">/</span>
                        <a href="{{ route('owner.dashboard') }}" class="text-xs font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                            Owner Portal
                        </a>
                        @if(request()->routeIs('spaces.builder'))
                            <span class="text-slate-300">/</span>
                            <span class="text-xs font-bold text-indigo-600">Visual Grid Builder</span>
                        @elseif(request()->routeIs('properties.index'))
                            <span class="text-slate-300">/</span>
                            <span class="text-xs font-bold text-indigo-600">My Properties</span>
                        @elseif(request()->routeIs('vendor.bookings'))
                            <span class="text-slate-300">/</span>
                            <span class="text-xs font-bold text-indigo-600">Booking Requests</span>
                        @elseif(request()->routeIs('vendor.seats'))
                            <span class="text-slate-300">/</span>
                            <span class="text-xs font-bold text-indigo-600">Seat Inventory</span>
                        @elseif(request()->routeIs('vendor.profile'))
                            <span class="text-slate-300">/</span>
                            <span class="text-xs font-bold text-indigo-600">Profile</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Property Owner</span>
                    </span>

                    <span class="text-xs font-mono-code text-slate-500 hidden lg:inline-block">
                        {{ now()->format('d M, Y') }}
                    </span>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 w-full">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
