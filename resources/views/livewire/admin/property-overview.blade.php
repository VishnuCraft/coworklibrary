<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                All Properties & Space Layouts
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Inspect physical buildings, cinema grid layouts, and active seats designed across the entire platform.
            </p>
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

    <!-- Filters & Search -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search bar -->
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
                placeholder="Search by property, vendor, or city..."
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition-all shadow-2xs"
            />
        </div>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <button 
                wire:click="$set('selectedCategory', '')"
                class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer {{ $selectedCategory === '' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                All Categories
            </button>
            @foreach([
                'library' => '📚 Library',
                'coworking' => '💼 Coworking',
                'house' => '🏡 House',
                'hostel' => '🛏️ Hostel',
                'bus' => '🚌 Bus',
            ] as $catKey => $catLabel)
                <button 
                    wire:click="$set('selectedCategory', '{{ $catKey }}')"
                    class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer {{ $selectedCategory === $catKey ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    {{ $catLabel }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="relative">
        <!-- Subtle Slim Loading Indicator Bar -->
        <div wire:loading wire:target="search, selectedCategory" class="absolute -top-3 left-0 right-0 h-0.5 bg-indigo-100 overflow-hidden rounded-full">
            <div class="h-full bg-indigo-600 animate-pulse w-full"></div>
        </div>

        <!-- Properties Cards Grid with Smooth Transition -->
        <div 
            wire:loading.class="opacity-50 pointer-events-none" 
            wire:target="search, selectedCategory" 
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-150">
            @forelse($properties as $property)
                @php
                    $totalFloors = $property->top_level_spaces_count;
                    $totalRows = $property->spaces->flatMap->seatRows->count();
                    $totalSeats = $property->spaces->flatMap->seats->where('is_walkway', false)->count();
                    $totalWalkways = $property->spaces->flatMap->seats->where('is_walkway', true)->count();
                @endphp
                <div class="rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                    <div class="p-5">
                        <!-- Category Badge & Delete -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold capitalize bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $property->category }}
                            </span>

                            <button 
                                wire:click="deleteProperty({{ $property->id }})"
                                wire:confirm="Permanently remove this property and all its layouts?"
                                class="text-slate-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors"
                                title="Delete Property">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        <!-- Property Name & Location -->
                        <h2 class="text-base font-bold text-slate-900 line-clamp-1">
                            {{ $property->name }}
                        </h2>
                        
                        @if($property->address)
                            <p class="mt-1 text-xs text-slate-500 truncate">
                                📍 {{ $property->address }}
                            </p>
                        @endif

                        <!-- Owner Pill -->
                        <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Owner / Vendor</span>
                            <div class="font-bold text-slate-800 truncate">{{ $property->user->name ?? 'Unknown' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono-code truncate">{{ $property->user->email ?? 'N/A' }}</div>
                        </div>

                        <!-- Layout Numbers -->
                        <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200/60">
                                <span class="block font-bold text-slate-900">{{ $totalFloors }}</span>
                                <span class="block text-[9px] text-slate-400 uppercase font-semibold">Floors</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200/60">
                                <span class="block font-bold text-slate-900">{{ $totalRows }}</span>
                                <span class="block text-[9px] text-slate-400 uppercase font-semibold">Rows</span>
                            </div>
                            <div class="bg-indigo-50/50 p-2 rounded-lg border border-indigo-100">
                                <span class="block font-bold text-indigo-700">{{ $totalSeats }}</span>
                                <span class="block text-[9px] text-indigo-600 uppercase font-semibold">
                                    Seats
                                    @if($totalWalkways > 0)
                                        <span class="text-amber-600 block text-[8px]">(+{{ $totalWalkways }})</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 font-mono-code">ID #{{ $property->id }}</span>
                        <a href="{{ route('spaces.builder', $property) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-all shadow-2xs">
                            <span>Inspect Builder</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                    No properties found matching criteria.
                </div>
            @endforelse
        </div>
    </div>
</div>
