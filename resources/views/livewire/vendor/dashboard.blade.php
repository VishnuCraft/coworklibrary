<div class="space-y-8">
    <!-- Vendor Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <span class="text-xl">👋</span>
            </div>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Space portfolio telemetry, cinema-style seat grids, and real-time occupancy overview.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('vendor.seats') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200/90 text-slate-700 hover:text-slate-900 hover:border-slate-300 font-semibold text-xs shadow-2xs hover:shadow-xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Seat Inventory</span>
            </a>

            <button 
                wire:click="openCreateModal"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-emerald-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Property</span>
            </button>
        </div>
    </div>

    <!-- Analytics Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Properties Card -->
        <a href="{{ route('properties.index') }}" class="group block p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-400 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">My Properties</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalProperties }}</span>
                <span class="text-xs text-slate-400">Listed spaces</span>
            </div>
            <div class="mt-2 text-[11px] text-indigo-600 font-semibold group-hover:underline flex items-center gap-1">
                <span>Manage all properties</span>
                <span>&rarr;</span>
            </div>
        </a>

        <!-- Total Bookable Seats -->
        <a href="{{ route('vendor.seats') }}" class="group block p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-emerald-400 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Bookable Seats</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalBookableSeats }}</span>
                <span class="text-xs text-emerald-600 font-medium">{{ $availableSeats }} Available</span>
            </div>
            <div class="mt-2 flex items-center gap-2 text-[11px] text-slate-500">
                <span class="text-rose-600 font-semibold">{{ $occupiedSeats }} Occupied</span>
                <span>&bull;</span>
                <span class="text-amber-600 font-semibold">{{ $totalWalkways }} Walkways</span>
            </div>
        </a>

        <!-- Occupancy Rate -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Occupancy Rate</span>
                <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $occupancyRate }}%</span>
                <span class="text-xs text-slate-400">Capacity utilization</span>
            </div>
            <!-- Progress Bar -->
            <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $occupancyRate) }}%"></div>
            </div>
        </div>

        <!-- Total Floors & Zones -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Floors &amp; Zones</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalFloors }}</span>
                <span class="text-xs text-slate-400">Design levels</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500">
                <span>Configured across {{ $totalProperties }} buildings</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <!-- LEFT 2/3 COLUMNS: Active Properties List -->
        <div class="lg:col-span-2 xl:col-span-3 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Your Properties Portfolio</h2>
                    <p class="text-xs text-slate-500">Instant access to visual builders, seat layouts, and floor architectures</p>
                </div>
                <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    View All ({{ $totalProperties }}) &rarr;
                </a>
            </div>

            @if($properties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($properties->take(6) as $property)
                        @php
                            $pSeats = $property->spaces->flatMap->seats->where('is_walkway', false);
                            $pAvailable = $pSeats->where('status', 'available')->count();
                            $pOccupied = $pSeats->where('status', 'occupied')->count();
                            $pTotal = $pSeats->count();
                            $pOccRate = $pTotal > 0 ? round(($pOccupied / $pTotal) * 100) : 0;
                        @endphp
                        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-400 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold capitalize 
                                        @if($property->category === 'library') bg-amber-50 text-amber-800 border border-amber-200
                                        @elseif($property->category === 'coworking') bg-indigo-50 text-indigo-800 border border-indigo-200
                                        @elseif($property->category === 'house' || $property->category === 'hostel') bg-emerald-50 text-emerald-800 border border-emerald-200
                                        @elseif($property->category === 'bus') bg-sky-50 text-sky-800 border border-sky-200
                                        @else bg-slate-100 text-slate-700 border border-slate-200
                                        @endif">
                                        <span class="w-1.5 h-1.5 rounded-full 
                                            @if($property->category === 'library') bg-amber-500
                                            @elseif($property->category === 'coworking') bg-indigo-500
                                            @elseif($property->category === 'house' || $property->category === 'hostel') bg-emerald-500
                                            @elseif($property->category === 'bus') bg-sky-500
                                            @else bg-slate-500
                                            @endif"></span>
                                        {{ $property->category }}
                                    </span>

                                    <span class="text-[11px] font-mono-code text-slate-400">#{{ $property->id }}</span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900 line-clamp-1">
                                    {{ $property->name }}
                                </h3>

                                @if($property->address)
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 truncate">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $property->address }}</span>
                                    </p>
                                @endif

                                <!-- Quick Metrics -->
                                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-center text-xs">
                                    <div class="bg-slate-50 p-2 rounded-xl">
                                        <span class="font-bold text-slate-800">{{ $property->top_level_spaces_count }}</span>
                                        <span class="block text-[10px] text-slate-500 uppercase font-semibold">Floors</span>
                                    </div>
                                    <div class="bg-indigo-50/50 p-2 rounded-xl">
                                        <span class="font-bold text-indigo-700">{{ $pTotal }}</span>
                                        <span class="block text-[10px] text-indigo-600/80 uppercase font-semibold">Bookable Seats</span>
                                    </div>
                                </div>

                                <!-- Mini Occupancy Bar -->
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 mb-1">
                                        <span>Occupancy</span>
                                        <span class="font-semibold text-slate-700">{{ $pOccRate }}% ({{ $pOccupied }}/{{ $pTotal }})</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-1 rounded-full" style="width: {{ $pOccRate }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Action Button -->
                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-end">
                                <a href="{{ route('spaces.builder', $property) }}" 
                                   class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs shadow-xs hover:bg-indigo-700 transition-all">
                                    <span>Open Visual Builder</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-10 text-center shadow-2xs">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 mx-auto flex items-center justify-center text-indigo-600 mb-3 shadow-2xs">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No properties created yet</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Start managing your coworking hub, study library, PG/hostel, or bus seats by creating your first property.
                    </p>
                    <div class="mt-4">
                        <button 
                            wire:click="openCreateModal"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow-xs hover:bg-indigo-700 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Create First Property</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Category Distribution & Quick Actions -->
        <div class="lg:col-span-1 xl:col-span-1 space-y-6">
            
            <!-- Quick Actions Card -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                <h3 class="text-sm font-bold text-slate-900">Quick Shortcuts</h3>
                <div class="space-y-2">
                    <button wire:click="openCreateModal" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-indigo-50/60 border border-slate-200 hover:border-indigo-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                +
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800 group-hover:text-indigo-700">Add New Property</span>
                                <span class="block text-[11px] text-slate-500">Register building or space</span>
                            </div>
                        </div>
                        <span class="text-slate-400 group-hover:text-indigo-600 text-sm">&rarr;</span>
                    </button>

                    <a href="{{ route('vendor.seats') }}" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                💺
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800 group-hover:text-emerald-700">Seat Inventory Status</span>
                                <span class="block text-[11px] text-slate-500">Live seat check-ins &amp; pricing</span>
                            </div>
                        </div>
                        <span class="text-slate-400 group-hover:text-emerald-600 text-sm">&rarr;</span>
                    </a>

                    <a href="{{ route('vendor.profile') }}" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center text-xs">
                                ⚙️
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800 group-hover:text-slate-900">Vendor Account Settings</span>
                                <span class="block text-[11px] text-slate-500">Update profile &amp; security</span>
                            </div>
                        </div>
                        <span class="text-slate-400 group-hover:text-slate-700 text-sm">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Categories Distribution -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Portfolio by Category</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span>📚</span>
                            <span>Study Libraries</span>
                        </span>
                        <span class="font-bold text-slate-900">{{ $categoryCounts['library'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span>💼</span>
                            <span>Coworking Spaces</span>
                        </span>
                        <span class="font-bold text-slate-900">{{ $categoryCounts['coworking'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span>🏡</span>
                            <span>House / Hostels</span>
                        </span>
                        <span class="font-bold text-slate-900">{{ $categoryCounts['hostel'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span>🚌</span>
                            <span>Bus / Transport Coaches</span>
                        </span>
                        <span class="font-bold text-slate-900">{{ $categoryCounts['bus'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span>🏢</span>
                            <span>Other Spaces</span>
                        </span>
                        <span class="font-bold text-slate-900">{{ $categoryCounts['other'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Property Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-3xl bg-white border border-slate-200 shadow-2xl p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Add New Property</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Enter the name and category for your building or vehicle.</p>
                    </div>
                    <button 
                        wire:click="closeCreateModal"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createProperty" class="mt-6 space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="vendor_prop_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Property Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="vendor_prop_name" 
                            wire:model="newPropertyName"
                            placeholder="e.g. Apex Study Library, Green Villa, Royal Travels" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        @error('newPropertyName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Category / Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                'library' => '📚 Study Library',
                                'coworking' => '💼 Coworking',
                                'house' => '🏡 House / PG',
                                'hostel' => '🛏️ Hostel',
                                'bus' => '🚌 Bus / Coach',
                                'other' => '🏢 Other Space'
                            ] as $catKey => $catLabel)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-medium transition-all
                                    {{ $newPropertyCategory === $catKey ? 'bg-indigo-50 border-indigo-500 text-indigo-900 font-semibold shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="newPropertyCategory" value="{{ $catKey }}" class="sr-only">
                                    <span>{{ $catLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('newPropertyCategory') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="vendor_prop_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Location / Address
                        </label>
                        <input 
                            type="text" 
                            id="vendor_prop_address" 
                            wire:model="newPropertyAddress"
                            placeholder="e.g. 2nd Cross, Near Metro Station" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="vendor_prop_desc" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Description / Notes
                        </label>
                        <textarea 
                            id="vendor_prop_desc" 
                            wire:model="newPropertyDescription"
                            rows="2"
                            placeholder="Optional notes or details about this property..." 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"></textarea>
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
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-emerald-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/25 hover:scale-[1.02] transition-all cursor-pointer">
                            Create &amp; Open Builder &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
