<div class="min-h-[80vh] flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-6">
        <!-- Brand Logo & Title -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-400 p-0.5 shadow-lg shadow-indigo-500/20 mb-4">
                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center">
                    @if($step === 1)
                        <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    @else
                        <svg class="w-7 h-7 text-indigo-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    @endif
                </div>
            </div>
            
            @if($step === 1)
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create your account</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Register to start managing your properties and cinema-style seat layouts.
                </p>
            @else
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Verify Your Email</h2>
                <p class="mt-2 text-sm text-slate-500">
                    We've sent a 6-digit OTP code to <strong class="text-slate-800">{{ $email }}</strong>.
                </p>
            @endif
        </div>

        <!-- Main Card Container -->
        <div class="bg-white border border-slate-200/90 shadow-xl shadow-slate-200/60 rounded-3xl p-8 sm:p-10">
            @if($step === 1)
                <!-- STEP 1: REGISTRATION FORM -->
                <form wire:submit="sendOtp" class="space-y-4">
                    <!-- Account Role Selection -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            I want to register as
                        </label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-2xl border border-slate-200">
                            <button type="button" 
                                    wire:click="$set('role', 'customer')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $role === 'customer' ? 'bg-white text-indigo-700 shadow-xs border border-indigo-100' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>🎓</span>
                                <span>Student / Aspirant</span>
                            </button>
                            <button type="button" 
                                    wire:click="$set('role', 'vendor')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $role === 'vendor' ? 'bg-white text-indigo-700 shadow-xs border border-indigo-100' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>🏢</span>
                                <span>Library Owner</span>
                            </button>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Full Name
                        </label>
                        <div class="mt-1.5 relative">
                            <input 
                                type="text" 
                                id="name" 
                                wire:model="name"
                                placeholder="e.g. John Doe, Pankaj Sharma" 
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        </div>
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

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

                    <!-- Mobile Number -->
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5 relative">
                            <input 
                                type="tel" 
                                id="phone" 
                                wire:model="phone"
                                placeholder="e.g. 9876543210" 
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        </div>
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- WhatsApp Number Toggle Card -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">💬</span>
                                <div>
                                    <span class="block text-xs font-bold text-slate-800">WhatsApp Number</span>
                                    <span class="block text-[11px] text-slate-500">Same as mobile number?</span>
                                </div>
                            </div>
                            
                            <!-- Toggle Button -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="is_whatsapp_same" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        @if($is_whatsapp_same)
                            <div class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mobile number will be used for WhatsApp updates.</span>
                            </div>
                        @else
                            <!-- Separate WhatsApp Number Field -->
                            <div class="pt-1.5 animate-in fade-in duration-150">
                                <label for="whatsapp_number" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                    WhatsApp Mobile Number <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="whatsapp_number" 
                                    wire:model="whatsapp_number"
                                    placeholder="e.g. 9876543210" 
                                    class="w-full rounded-xl bg-white border border-slate-300 px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                                @error('whatsapp_number') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        @endif
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
                                placeholder="At least 6 characters" 
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        </div>
                        @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Confirm Password
                        </label>
                        <div class="mt-1.5 relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                wire:model="password_confirmation"
                                placeholder="Repeat password" 
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        </div>
                    </div>

                    <!-- Send OTP Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 text-white font-semibold text-sm shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer disabled:opacity-60">
                            <span wire:loading.remove wire:target="sendOtp">Send Email OTP &rarr;</span>
                            <span wire:loading wire:target="sendOtp" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Sending OTP...
                            </span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
                    Already registered? 
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 underline">
                        Log in to your account
                    </a>
                </div>
            @else
                <!-- STEP 2: OTP VERIFICATION FORM -->
                @if($statusMessage)
                    <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $statusMessage }}</span>
                    </div>
                @endif

                <form wire:submit="verifyOtp" class="space-y-5">
                    <div>
                        <label for="otp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 text-center">
                            Enter 6-Digit Verification Code
                        </label>
                        <div class="mt-3">
                            <input 
                                type="text" 
                                id="otp" 
                                wire:model="otp"
                                maxlength="6"
                                autofocus
                                placeholder="• • • • • •" 
                                class="w-full text-center tracking-[12px] font-mono font-bold text-2xl rounded-2xl bg-slate-50 border-2 border-indigo-200 py-3.5 text-indigo-900 placeholder-slate-300 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 outline-none transition-all" />
                        </div>
                        @error('otp') <span class="text-xs text-rose-500 mt-2 block font-medium text-center">{{ $message }}</span> @enderror
                    </div>

                    <!-- Verify Button -->
                    <div>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 text-white font-semibold text-sm shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer disabled:opacity-60">
                            <span wire:loading.remove wire:target="verifyOtp">Verify OTP & Activate Account &rarr;</span>
                            <span wire:loading wire:target="verifyOtp" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Verifying...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Actions: Resend OTP and Edit Email -->
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <button 
                        type="button" 
                        wire:click="resendOtp"
                        wire:loading.attr="disabled"
                        class="text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer underline flex items-center gap-1">
                        <span wire:loading.remove wire:target="resendOtp">Resend OTP</span>
                        <span wire:loading wire:target="resendOtp">Sending fresh OTP...</span>
                    </button>

                    <button 
                        type="button" 
                        wire:click="backToStep1"
                        class="text-slate-500 hover:text-slate-800 font-medium cursor-pointer">
                        &larr; Change Email
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
