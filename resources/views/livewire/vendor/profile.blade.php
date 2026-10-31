<div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="pb-6 border-b border-slate-200">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
            Vendor Profile &amp; Account Settings
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Manage your personal profile, email credentials, and workspace security.
        </p>
    </div>

    @if($statusMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between animate-in fade-in duration-200">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ $statusMessage }}</span>
            </div>
            <button wire:click="$set('statusMessage', null)" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">
                &times;
            </button>
        </div>
    @endif

    <!-- Profile Overview Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-500 text-white flex items-center justify-center font-extrabold text-xl shadow-md shadow-indigo-500/20">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 font-mono-code">{{ $user->email }}</p>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Space Owner &bull; Vendor
                    </span>
                    @if($user->phone)
                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-600 font-mono-code bg-slate-100 px-2 py-0.5 rounded-md">
                            📞 {{ $user->phone }}
                        </span>
                    @endif
                    @if($user->whatsapp_number)
                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-mono-code bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                            💬 {{ $user->whatsapp_number }}
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400">
                        Joined {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 text-center">
            <div class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <span class="block text-base font-extrabold text-slate-900">{{ $propertiesCount }}</span>
                <span class="block text-[10px] uppercase font-bold text-slate-500">Properties</span>
            </div>
            <div class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <span class="block text-base font-extrabold text-slate-900">{{ $seatsCount }}</span>
                <span class="block text-[10px] uppercase font-bold text-slate-500">Seats Managed</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profile Info Form -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Personal Information</h3>
            <p class="text-xs text-slate-500 mb-4">Update your display name and login email.</p>

            <form wire:submit="updateProfile" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        wire:model="email" 
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Mobile Number -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Mobile Number
                    </label>
                    <input 
                        type="tel" 
                        wire:model="phone" 
                        placeholder="e.g. 9876543210"
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
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
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                WhatsApp Mobile Number <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                wire:model="whatsapp_number"
                                placeholder="e.g. 9876543210" 
                                class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 outline-none" />
                            @error('whatsapp_number') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-xs transition-all cursor-pointer">
                        Update Information
                    </button>
                </div>
            </form>
        </div>

        <!-- Security / Password Form -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Security &amp; Password</h3>
            <p class="text-xs text-slate-500 mb-4">Ensure your vendor account uses a strong password.</p>

            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        wire:model="current_password" 
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    @error('current_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        wire:model="password" 
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        wire:model="password_confirmation" 
                        class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition-all cursor-pointer">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
