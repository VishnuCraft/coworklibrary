<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Administrators
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Manage administrative personnel. Frontend registration is strictly for vendors; administrators can only be created here.
            </p>
        </div>

        <div>
            <button 
                id="btn-create-admin"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-900 text-white font-semibold text-xs shadow-md shadow-indigo-950/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Create Administrator</span>
            </button>
        </div>
    </div>

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

    <!-- Search & Filters -->
    <div class="flex items-center justify-between gap-4">
        <div class="relative max-w-sm w-full">
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
                wire:model.live.debounce.300ms="search" 
                placeholder="Search administrators by name or email..."
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition-all shadow-2xs"
            />
        </div>
        
        <span class="text-xs text-slate-400 font-medium">
            <span wire:loading.remove wire:target="search">Total {{ $admins->count() }} accounts</span>
            <span wire:loading wire:target="search" class="text-indigo-600 font-semibold animate-pulse">Filtering admins...</span>
        </span>
    </div>

    <!-- Admins Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <!-- Slim Loading Progress Bar -->
        <div wire:loading wire:target="search" class="h-0.5 w-full bg-slate-100 overflow-hidden">
            <div class="h-full bg-indigo-600 animate-pulse w-full"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-6">Administrator</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Account Status</th>
                        <th class="py-3 px-4">Created Date</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="search" class="divide-y divide-slate-100 transition-opacity duration-150">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs uppercase shrink-0 bg-violet-100 text-violet-700 border border-violet-200">
                                        {{ substr($admin->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $admin->name }}</span>
                                            @if($admin->id === auth()->id())
                                                <span class="text-[9px] uppercase px-1.5 py-0.2 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold">You</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono-code">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-violet-50 text-violet-700 border border-violet-200">
                                    <span>🛡️</span>
                                    <span>Administrator</span>
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $admin->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $admin->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-500 font-mono-code text-[11px]">
                                {{ $admin->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($admin->id !== auth()->id())
                                    <div class="inline-flex items-center gap-2">
                                        <button 
                                            wire:click="toggleStatus({{ $admin->id }})"
                                            class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-colors cursor-pointer {{ $admin->is_active ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $admin->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                        <button 
                                            wire:click="deleteAdmin({{ $admin->id }})"
                                            wire:confirm="Are you sure you want to permanently delete administrator {{ $admin->name }}?"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Delete Administrator">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Active (Current)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No administrators found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE ADMIN MODAL -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white border border-slate-200 shadow-2xl p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Create Administrator</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Add an administrator account with panel access.</p>
                    </div>
                    <button wire:click="closeCreateModal" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createAdmin" class="mt-5 space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label for="create_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_name" 
                            wire:model="name"
                            placeholder="e.g. Administrator Name" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all" />
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="create_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="create_email" 
                            wire:model="email"
                            placeholder="admin@coworklibrary.com" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all" />
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="create_pw" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Set Password <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="create_pw" 
                            wire:model="password"
                            placeholder="Minimum 6 characters" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all" />
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="create_pw_conf" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="create_pw_conf" 
                            wire:model="password_confirmation"
                            placeholder="Repeat password" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition-all" />
                    </div>

                    <!-- Action buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="closeCreateModal"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-900 text-white font-semibold text-xs shadow-md shadow-indigo-950/20 hover:scale-[1.02] transition-all cursor-pointer">
                            Create Administrator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
