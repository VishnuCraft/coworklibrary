<div class="min-h-[80vh] flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-6">
        <!-- Logo & Title -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-400 p-0.5 shadow-lg shadow-indigo-500/20 mb-4">
                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome back</h2>
            <p class="mt-2 text-sm text-slate-500">
                Log in to your CoworkLibrary account to access your spaces.
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200/90 shadow-xl shadow-slate-200/60 rounded-3xl p-8 sm:p-10">
            <form wire:submit="login" class="space-y-4">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Email Address
                    </label>
                    <div class="mt-1.5 relative">
                        <input 
                            type="email" 
                            id="email" 
                            wire:model="email"
                            placeholder="name@example.com" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>
                    @error('email') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Password
                    </label>
                    <div class="mt-1.5 relative">
                        <input 
                            type="password" 
                            id="password" 
                            wire:model="password"
                            placeholder="••••••••" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>
                    @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Remember my login</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 text-white font-semibold text-sm shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                        <span>Sign In</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
                Don't have an account yet? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 underline">
                    Create new account
                </a>
            </div>
        </div>
    </div>
</div>
