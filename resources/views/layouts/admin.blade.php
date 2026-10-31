<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 text-slate-800">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Super Admin Panel - CoworkLibrary' }}</title>

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

        <!-- SIDEBAR -->
        <aside 
            :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between transition-transform duration-200 ease-in-out md:static md:translate-x-0 shrink-0">
            
            <div>
                <!-- Admin Brand Header -->
                <div class="h-16 px-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-600 via-indigo-600 to-amber-400 p-0.5 shadow-md shadow-indigo-500/20">
                            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center text-indigo-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-extrabold tracking-tight text-white flex items-center gap-1.5">
                                CWL<span class="text-indigo-400">Admin</span>
                            </span>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-400">
                                Administrator
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
                        Main Menu
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- All Users -->
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.users') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>All Users</span>
                    </a>

                    <!-- Vendors (Registered Property Owners) -->
                    <a href="{{ route('admin.vendors') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.vendors') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Vendors / Owners</span>
                    </a>

                    <!-- Properties & Layouts Overview -->
                    <a href="{{ route('admin.properties') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.properties') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>All Properties</span>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Access & Security
                    </div>

                    <!-- Administrators -->
                    <a href="{{ route('admin.admins') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.admins') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Administrators</span>
                    </a>

                    <!-- Switch to Owner Portal -->
                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Quick Links
                    </div>
                    <a href="{{ route('owner.dashboard') }}" target="_blank"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-emerald-400 hover:bg-slate-800/60 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Owner Portal &nearr;</span>
                    </a>
                </div>
            </div>

            <!-- Logged In Admin Profile & Sign Out Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-semibold text-white truncate">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </span>
                            <span class="block text-[10px] text-amber-400 capitalize">
                                Administrator
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors"
                                title="Sign out of Admin Panel">
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
            @if(session()->has('impersonator_id'))
                <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-indigo-600 text-white px-4 sm:px-6 lg:px-8 py-2.5 text-xs font-semibold shadow-xs border-b border-amber-400/30 shrink-0">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white font-extrabold text-[10px] uppercase tracking-wider shrink-0">
                                Switched User Mode
                            </span>
                            <span class="truncate">
                                Currently operating as <strong>{{ auth()->user()->name }}</strong> (<span class="font-mono-code">{{ auth()->user()->email }}</span>).
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.switch-back') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white text-slate-900 font-bold hover:bg-amber-50 transition-colors shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Return to Original Admin Account &rarr;</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Admin Topbar -->
            <header class="h-16 bg-white border-b border-slate-200/90 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-3">
                    <button @click="mobileNavOpen = true" class="md:hidden text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-xs font-semibold text-slate-500 hidden sm:inline-block">
                        CoworkLibrary Central Administration
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-600 animate-pulse"></span>
                        <span>Administrator Panel</span>
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
