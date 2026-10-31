<div class="space-y-8">
    <!-- Header with Stats & Call to Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                Property Portfolio
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage your physical spaces, buildings, coworking hubs, libraries, or transport layouts.
            </p>
        </div>

        <div>
            <button 
                id="btn-add-property"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Property</span>
            </button>
        </div>
    </div>

    <!-- Quick Portfolio Metrics -->
    @php
        $totalPropertiesCount = $properties->count();
        $totalFloorsCount = $properties->sum('top_level_spaces_count');
        $allSeatsCollection = $properties->flatMap->spaces->flatMap->seats;
        $totalBookableSeats = $allSeatsCollection->where('is_walkway', false)->count();
        $totalWalkwaysCount = $allSeatsCollection->where('is_walkway', true)->count();
        $availableSeatsCount = $allSeatsCollection->where('is_walkway', false)->where('status', 'available')->count();
        $occupiedSeatsCount = $allSeatsCollection->where('is_walkway', false)->where('status', 'occupied')->count();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Properties</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalPropertiesCount }}</span>
            <span class="text-[11px] text-slate-400">Listed physical spaces</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Floors</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalFloorsCount }}</span>
            <span class="text-[11px] text-slate-400">Design levels &amp; zones</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-emerald-700">Total Bookable Seats</span>
            <span class="text-2xl font-extrabold text-emerald-800 mt-1 block">{{ $totalBookableSeats }}</span>
            <span class="text-[11px] text-emerald-600 font-medium">{{ $availableSeatsCount }} Available &bull; {{ $occupiedSeatsCount }} Occupied</span>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-700">Aisles &amp; Walkways</span>
            <span class="text-2xl font-extrabold text-amber-800 mt-1 block">{{ $totalWalkwaysCount }}</span>
            <span class="text-[11px] text-amber-600">Free passage aisles</span>
        </div>
    </div>

    <!-- Properties Grid -->
    @if($properties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <div class="group relative rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-400 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    <!-- Accent top color strip -->
                    <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-violet-500 to-amber-400"></div>

                    <div class="p-6">
                        <!-- Category Badge & Action -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold capitalize 
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

                            <button 
                                wire:click="deleteProperty({{ $property->id }})"
                                wire:confirm="Are you sure you want to delete this property and all its spaces and seat layouts?"
                                class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors"
                                title="Delete Property">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        <!-- Title & Details -->
                        <h2 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            {{ $property->name }}
                        </h2>
                        
                        @if($property->address)
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $property->address }}</span>
                            </p>
                        @endif

                        @if($property->description)
                            <p class="mt-2 text-xs text-slate-600 line-clamp-2">
                                {{ $property->description }}
                            </p>
                        @endif

                        <!-- Metrics & Quick Counts -->
                        @php
                            $totalFloors = $property->top_level_spaces_count;
                            $totalRows = $property->spaces->flatMap->seatRows->count();
                            $totalSeats = $property->spaces->flatMap->seats->where('is_walkway', false)->count();
                            $totalWalkways = $property->spaces->flatMap->seats->where('is_walkway', true)->count();
                        @endphp
                        <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                                <span class="block text-base font-bold text-slate-900">{{ $totalFloors }}</span>
                                <span class="block text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Floors</span>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                                <span class="block text-base font-bold text-slate-900">{{ $totalRows }}</span>
                                <span class="block text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Rows</span>
                            </div>
                            <div class="bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-100">
                                <span class="block text-base font-bold text-indigo-700">{{ $totalSeats }}</span>
                                <span class="block text-[10px] text-indigo-600/80 uppercase tracking-wider font-semibold">
                                    Total Seats
                                    @if($totalWalkways > 0)
                                        <span class="text-amber-600 font-normal block text-[9px]">(+{{ $totalWalkways }} galli)</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-mono-code font-medium">ID #{{ $property->id }}</span>
                        <a href="{{ route('spaces.builder', $property) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-all shadow-xs">
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
        <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-white p-12 text-center max-w-xl mx-auto my-12 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 mx-auto flex items-center justify-center text-indigo-600 mb-4 shadow-xs">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">No Properties Listed Yet</h3>
            <p class="mt-2 text-sm text-slate-500">
                Get started by creating your first property (e.g. Study Library, Coworking Hub, House, or Bus). You can then design its floors, halls, and cinema-style seat grids visually!
            </p>
            <div class="mt-6">
                <button 
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:scale-105 transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Your First Property</span>
                </button>
            </div>
        </div>
    @endif

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
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createProperty" class="mt-6 space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="prop_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Property Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="prop_name" 
                            wire:model="name"
                            placeholder="e.g. Apex Study Library, Green Villa, Royal Travels" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
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
                                    {{ $category === $catKey ? 'bg-indigo-50 border-indigo-500 text-indigo-900 font-semibold shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="category" value="{{ $catKey }}" class="sr-only">
                                    <span>{{ $catLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('category') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="prop_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Location / Address
                        </label>
                        <input 
                            type="text" 
                            id="prop_address" 
                            wire:model="address"
                            placeholder="e.g. 2nd Cross, Near Metro Station" 
                            class="mt-1.5 w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="prop_desc" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Description / Notes
                        </label>
                        <textarea 
                            id="prop_desc" 
                            wire:model="description"
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
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/25 hover:shadow-indigo-600/35 hover:scale-[1.02] transition-all cursor-pointer">
                            Create & Open Builder &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
