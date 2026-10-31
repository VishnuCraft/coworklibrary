<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Seat Inventory &amp; Live Occupancy
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Manage seat availability, student check-ins, prices, and notes across all your spaces.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('properties.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200/90 text-slate-700 hover:text-slate-900 font-semibold text-xs shadow-2xs hover:shadow-xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>My Properties</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Bookable</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $realSeatsCount }}</span>
            <span class="text-[11px] text-slate-400">Excludes walkways</span>
        </div>

        <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/80 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-emerald-700">Available</span>
            <span class="text-2xl font-extrabold text-emerald-800 mt-1 block">{{ $availableCount }}</span>
            <span class="text-[11px] text-emerald-600 font-medium">Ready for booking</span>
        </div>

        <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-200/80 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-rose-700">Occupied</span>
            <span class="text-2xl font-extrabold text-rose-800 mt-1 block">{{ $occupiedCount }}</span>
            <span class="text-[11px] text-rose-600 font-medium">Currently active</span>
        </div>

        <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-200/80 shadow-2xs">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-700">Aisles / Walkways</span>
            <span class="text-2xl font-extrabold text-amber-800 mt-1 block">{{ $walkwayCount }}</span>
            <span class="text-[11px] text-amber-600">Free passage</span>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
            <!-- Search -->
            <div class="relative flex-1 max-w-xs">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search seat number or space..." 
                    class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all" />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Property Filter -->
            <select 
                wire:model.live="selectedPropertyId"
                class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:bg-white focus:border-indigo-500 outline-none transition-all cursor-pointer">
                <option value="">All Properties ({{ $properties->count() }})</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select 
                wire:model.live="statusFilter"
                class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:bg-white focus:border-indigo-500 outline-none transition-all cursor-pointer">
                <option value="all">All Seats</option>
                <option value="available">🟢 Available</option>
                <option value="occupied">🔴 Occupied</option>
                <option value="maintenance">🟡 Maintenance</option>
                <option value="walkway">⚪ Walkways / Galli</option>
            </select>
        </div>

        <div class="text-xs text-slate-400">
            Showing {{ $seats->total() }} total seats
        </div>
    </div>

    <!-- Seats Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden relative"
         wire:loading.class="opacity-60 pointer-events-none"
         wire:target="search, selectedPropertyId, statusFilter">
        
        <!-- Subtle Top Loading Line -->
        <div wire:loading wire:target="search, selectedPropertyId, statusFilter" class="absolute top-0 inset-x-0 h-0.5 bg-indigo-600 animate-pulse"></div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-6">Seat #</th>
                        <th class="py-3 px-4">Property &amp; Space</th>
                        <th class="py-3 px-4">Section / Row</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Price &amp; Notes</th>
                        <th class="py-3 px-6 text-right">Quick Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($seats as $seat)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Seat Number -->
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs
                                        @if($seat->is_walkway) bg-slate-100 text-slate-400 border border-slate-200
                                        @elseif($seat->status === 'available') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($seat->status === 'occupied') bg-rose-50 text-rose-700 border border-rose-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200
                                        @endif">
                                        @if($seat->is_walkway)
                                            <span class="text-[10px]">WALK</span>
                                        @else
                                            💺
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 font-mono-code text-sm">
                                            {{ $seat->seat_number }}
                                        </div>
                                        @if($seat->is_walkway)
                                            <span class="text-[10px] text-slate-400">Aisle / Galli</span>
                                        @else
                                            <span class="text-[10px] text-slate-500 capitalize">{{ $seat->type }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Property & Space -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ $seat->space->property->name ?? 'N/A' }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $seat->space->name ?? 'Floor' }}
                                </div>
                            </td>

                            <!-- Section / Row -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-700">
                                    {{ $seat->seatRow->row_label ?? 'Row' }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $seat->seatRow->section_name ?? 'Main Section' }} (Col {{ $seat->column_index }})
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($seat->is_walkway)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Aisle / Walkway
                                    </span>
                                @elseif($seat->status === 'available')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Available</span>
                                    </span>
                                @elseif($seat->status === 'occupied')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Occupied</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Maintenance</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Price & Notes -->
                            <td class="py-3.5 px-4">
                                @if($seat->price)
                                    <div class="font-bold text-slate-800">
                                        ₹{{ number_format($seat->price, 2) }}
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Not set</span>
                                @endif

                                @if($seat->notes)
                                    <div class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $seat->notes }}">
                                        {{ $seat->notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-right space-x-1.5">
                                @if(! $seat->is_walkway)
                                    @if($seat->status === 'available')
                                        <button 
                                            wire:click="updateSeatStatus({{ $seat->id }}, 'occupied')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white text-[11px] font-semibold transition-all shadow-2xs cursor-pointer"
                                            title="Check-in student">
                                            <span>Check-in</span>
                                        </button>
                                    @elseif($seat->status === 'occupied')
                                        <button 
                                            wire:click="updateSeatStatus({{ $seat->id }}, 'available')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white text-[11px] font-semibold transition-all shadow-2xs cursor-pointer"
                                            title="Vacate seat">
                                            <span>Vacate</span>
                                        </button>
                                    @endif

                                    <button 
                                        wire:click="openEditModal({{ $seat->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Edit Price & Notes">
                                        <span>Edit</span>
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Walkway</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto">
                                    <p class="font-bold text-slate-700 text-sm">No seats found</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        @if($properties->count() === 0)
                                            You haven't created any properties or spaces yet.
                                        @else
                                            No seats match your selected property or filter criteria.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($seats->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $seats->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Seat Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white border border-slate-200 shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Edit Seat Details</h3>
                    <button wire:click="closeEditModal" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveSeat" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Seat Number / Label
                        </label>
                        <input 
                            type="text" 
                            wire:model="editSeatNumber" 
                            class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                        @error('editSeatNumber') <span class="text-xs text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Status
                        </label>
                        <select wire:model="editSeatStatus" class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="available">🟢 Available</option>
                            <option value="occupied">🔴 Occupied</option>
                            <option value="maintenance">🟡 Maintenance</option>
                        </select>
                        @error('editSeatStatus') <span class="text-xs text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Price (₹ / month or day)
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            wire:model="editSeatPrice" 
                            placeholder="e.g. 1500" 
                            class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                        @error('editSeatPrice') <span class="text-xs text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Notes / Assigned Student Name
                        </label>
                        <textarea 
                            wire:model="editSeatNotes" 
                            rows="2" 
                            placeholder="Optional notes or student assigned..." 
                            class="mt-1 w-full rounded-xl bg-slate-50 border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none"></textarea>
                        @error('editSeatNotes') <span class="text-xs text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" wire:click="closeEditModal" class="px-3 py-1.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
