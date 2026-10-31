<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-900 via-indigo-950 to-violet-900 border border-slate-700/60 shadow-xl shadow-indigo-900/20 mb-4 text-amber-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight sm:text-3xl">
            Administrator Portal
        </h1>
        <p class="mt-1.5 text-xs text-slate-500 font-medium">
            Restricted Central Management &bull; CoworkLibrary Network
        </p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        <!-- Security notice banner -->
        <div class="mb-6 p-3 rounded-xl bg-violet-50/80 border border-violet-100 flex items-center gap-2.5 text-xs text-violet-900 font-medium">
            <svg class="w-4 h-4 text-violet-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Only Super Admin and assigned Admins can authenticate here.</span>
        </div>

        <form wire:submit="login" class="space-y-4">
            <!-- Email -->
            <div>
                <label for="admin_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Admin Email
                </label>
                <input 
                    type="email" 
                    id="admin_email" 
                    wire:model="email" 
                    placeholder="admin@coworklibrary.com"
                    class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all"
                    required
                />
                @error('email') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="admin_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Security Password
                </label>
                <input 
                    type="password" 
                    id="admin_password" 
                    wire:model="password" 
                    placeholder="••••••••"
                    class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all"
                    required
                />
                @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" wire:model="remember" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" />
                    <span>Keep admin session active</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    id="btn-admin-login"
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-900 text-white font-semibold text-sm shadow-md shadow-indigo-950/20 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Authenticate into Admin Panel</span>
                </button>
            </div>
        </form>

        <!-- Pre-configured Credentials Quick Card -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Default Administrator Access:
            </span>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between font-semibold text-slate-800 text-xs">
                    <span>🛡️ Administrator</span>
                    <span class="text-[10px] font-mono-code text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">PORTAL ACCESS</span>
                </div>
                <div class="text-xs text-slate-600 font-mono-code mt-1">
                    <strong>Email:</strong> admin@coworklibrary.com<br>
                    <strong>Password:</strong> admin123
                </div>
            </div>
        </div>
    </div>

    <!-- Switch back to vendor portal -->
    <div class="mt-6 text-center text-xs text-slate-400">
        Are you a Vendor / Space Owner? 
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 underline">
            Go to Vendor Login &rarr;
        </a>
    </div>
</div>
