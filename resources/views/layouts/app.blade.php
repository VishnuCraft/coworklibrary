<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'CoworkLibrary - Visual Space & Seat Management' }}</title>

        <!-- Google Fonts: Inter & Plus Jakarta Sans -->
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
    <body class="min-h-full flex flex-col bg-slate-50/70 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white">
        @if(session()->has('impersonator_id'))
            <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-indigo-600 text-white px-4 py-2 text-xs font-semibold shadow-md sticky top-0 z-50">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full bg-white/20 text-white font-extrabold text-[10px] uppercase tracking-wider">
                            Switched User Mode
                        </span>
                        <span>
                            You are logged in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).
                        </span>
                    </div>
                    <form method="POST" action="{{ route('admin.switch-back') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-lg bg-white text-slate-900 font-bold hover:bg-amber-50 transition-colors shadow-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Return to Admin Panel &rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-md shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-400 p-0.5 shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-slate-900 flex items-center gap-1.5">
                                Cowork<span class="text-indigo-600">Library</span>
                            </span>
                            <span class="block text-[11px] text-slate-500 font-medium">Real-Time Cinema-Style Seat Booking</span>
                        </div>
                    </a>
                </div>

                <!-- Nav & User Profile -->
                <nav class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('home') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Explore Spaces
                    </a>

                    @auth
                        <a href="{{ route('customer.bookings') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('customer.bookings') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            My Bookings
                        </a>

                        @if(auth()->user()->isVendor() || auth()->user()->isAdmin())
                            <a href="{{ route('owner.dashboard') }}" 
                               class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                                <span>🏢 Owner Portal</span>
                                <span class="text-[10px]">&rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('properties.index') }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-white text-xs font-extrabold transition-all shadow-xs hover:shadow-sm"
                               title="List your library, study hall or coworking hub">
                                <span>✨ Become an Owner</span>
                                <span class="text-[9px] bg-white/20 px-1.5 py-0.2 rounded-full font-bold uppercase tracking-wider hidden sm:inline">List Space</span>
                            </a>
                        @endif

                        <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                        <!-- User Profile & Logout -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-800 hidden sm:inline-block">
                                {{ auth()->user()->name }}
                            </span>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" 
                                    class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 rounded-lg transition-colors cursor-pointer"
                                    title="Sign Out">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all">
                            Register
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
            CoworkLibrary &bull; Hierarchical Space & Seat Architecture &bull; Laravel + Livewire + Tailwind
        </footer>

        @livewireScripts
    </body>
</html>
