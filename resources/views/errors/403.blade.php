<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>403 - Access Restricted | CoworkLibrary</title>

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
    </head>
    <body class="min-h-full flex flex-col justify-between bg-slate-950 text-slate-100 antialiased relative overflow-x-hidden selection:bg-rose-500 selection:text-white">
        
        <!-- Ambient Background Glows -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[650px] h-[350px] bg-gradient-to-tr from-rose-600/20 via-violet-600/20 to-indigo-600/20 blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-gradient-to-br from-indigo-600/15 via-amber-500/10 to-transparent blur-[140px] pointer-events-none"></div>

        <!-- Top Header Navigation -->
        <header class="relative z-10 max-w-6xl mx-auto w-full px-6 py-6 flex items-center justify-between">
            <a href="{{ route('properties.index') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-600 via-indigo-600 to-amber-400 p-0.5 shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-tight text-white flex items-center gap-1">
                        Cowork<span class="text-indigo-400">Library</span>
                    </span>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Space &amp; Seat Management
                    </span>
                </div>
            </a>

            @if(session()->has('impersonator_id'))
                <form method="POST" action="{{ route('admin.switch-back') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs hover:from-amber-400 hover:to-amber-500 transition-all shadow-md shadow-amber-500/20 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Return to Admin Panel</span>
                    </button>
                </form>
            @endif
        </header>

        <!-- Main Card Content -->
        <main class="relative z-10 max-w-xl mx-auto w-full px-6 py-10 flex flex-col items-center text-center">
            
            <!-- Shield / Lock Graphic -->
            <div class="relative mb-6">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-rose-500/20 via-rose-500/10 to-amber-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 shadow-xl shadow-rose-950/40">
                    <svg class="w-10 h-10 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="absolute -bottom-2 -right-2 px-2 py-0.5 rounded-full bg-rose-500 text-white font-extrabold text-[10px] tracking-wider uppercase shadow-md">
                    403
                </div>
            </div>

            <!-- Error Code Pill -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-rose-400 bg-rose-500/10 border border-rose-500/20 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                <span>Access Restricted</span>
            </span>

            <!-- Main Title -->
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-3">
                Administrator Access Required
            </h1>

            <!-- Exception / Description Message -->
            <p class="text-sm text-slate-400 max-w-md mx-auto mb-6 leading-relaxed">
                {{ $exception->getMessage() ?: 'Unauthorized access. This area is reserved for Administrators only.' }}
            </p>

            <!-- Special Impersonation Card (If User is Currently Switched) -->
            @if(session()->has('impersonator_id'))
                <div class="w-full mb-8 p-5 rounded-2xl bg-gradient-to-b from-amber-500/15 to-amber-950/20 border border-amber-500/30 text-left space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-300">
                                Switched User Mode Active
                            </span>
                        </div>
                        <span class="text-[11px] font-mono-code text-amber-400/80">Impersonating</span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        Aap abhi vendor account <strong>{{ auth()->user()->name }}</strong> (<span class="font-mono-code text-amber-300">{{ auth()->user()->email }}</span>) ke roop me logged in hain. Administrator area access karne ke liye apne main Administrator account par wapas lautein.
                    </p>

                    <form method="POST" action="{{ route('admin.switch-back') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Return to Administrator Panel Now</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('owner.dashboard') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/30 hover:bg-indigo-500 active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Owner Dashboard</span>
                </a>

                <a href="{{ route('admin.login') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 hover:border-slate-700 font-semibold text-xs transition-all">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Administrator Sign In</span>
                </a>

                <button onclick="window.history.back()" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 text-xs font-semibold hover:bg-slate-900 transition-all cursor-pointer">
                    <span>&larr; Go Back</span>
                </button>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 max-w-6xl mx-auto w-full px-6 py-6 text-center text-xs text-slate-400 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; {{ date('Y') }} CoworkLibrary Central Administration</span>
            <span class="font-mono-code text-[11px] text-slate-400">HTTP 403 Forbidden &bull; Protected Access</span>
        </footer>
    </body>
</html>
