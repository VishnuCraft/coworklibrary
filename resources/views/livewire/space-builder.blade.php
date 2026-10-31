<div class="space-y-6">
    <!-- Property & Breadcrumb Navigation Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider">
                <a href="{{ route('properties.index') }}" class="hover:underline flex items-center gap-1 text-slate-500 hover:text-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Properties
                </a>
                <span class="text-slate-300">/</span>
                <span>{{ $property->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 flex items-center gap-3">
                <span>{{ $property->name }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $property->category }}
                </span>
            </h1>
        </div>

        <!-- Layout Metric Badges & Bulk Action -->
        <div class="flex flex-wrap items-center gap-2.5">
            <div class="flex items-center gap-3 bg-white border border-slate-200 px-4 py-2 rounded-xl text-xs shadow-xs">
                <div>
                    <span class="text-slate-500">Total Seats:</span>
                    <span class="font-bold text-slate-900 ml-1">{{ $totalSeats }}</span>
                </div>
                <div class="h-3 w-px bg-slate-200"></div>
                <div>
                    <span class="text-emerald-600">Available:</span>
                    <span class="font-bold text-slate-900 ml-1">{{ $availableSeats }}</span>
                </div>
                <div class="h-3 w-px bg-slate-200"></div>
                <div>
                    <span class="text-amber-600">Walkways:</span>
                    <span class="font-bold text-slate-900 ml-1">{{ $totalWalkways }}</span>
                </div>
            </div>

            @if($currentFloor)
                <button 
                    id="btn-bulk-generator"
                    wire:click="$set('showBulkModal', true)"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-violet-50 border border-violet-200 text-violet-700 hover:bg-violet-600 hover:text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Bulk Generator (Rows A-E)</span>
                </button>
            @endif
        </div>
    </div>

    <!-- FLOOR / LEVEL NAVIGATION TABS -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Select Floor / Level
            </span>
            <button 
                id="btn-add-floor"
                wire:click="$set('showAddFloorModal', true)"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-semibold hover:bg-indigo-600 hover:text-white transition-all cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Floor</span>
            </button>
        </div>

        @if($floors->count() > 0)
            <div class="flex flex-wrap items-center gap-2">
                @foreach($floors as $floor)
                    <div class="inline-flex items-center rounded-xl transition-all {{ $selectedFloorId === $floor->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300' }}">
                        <button 
                            wire:click="selectFloor({{ $floor->id }})"
                            class="px-4 py-2 text-xs font-semibold flex items-center gap-2 cursor-pointer">
                            <span>{{ $floor->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedFloorId === $floor->id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                                {{ $floor->type }}
                            </span>
                        </button>
                        <button 
                            wire:click="deleteFloor({{ $floor->id }})"
                            wire:confirm="Delete this floor and all its rows and seats?"
                            class="pr-2.5 pl-1 py-2 text-xs {{ $selectedFloorId === $floor->id ? 'text-indigo-200 hover:text-white' : 'text-slate-400 hover:text-rose-600' }}"
                            title="Delete floor">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-xs text-slate-500">
                No floors created yet. Click <span class="text-indigo-600 font-semibold cursor-pointer" wire:click="$set('showAddFloorModal', true)">+ Add Floor</span> (e.g. Ground Floor, 1st Floor, Upper Deck) to get started.
            </div>
        @endif
    </div>

    <!-- MAIN AREA: ROW ADDITION TOOLBAR & CINEMA SEAT GRID FOR CURRENT FLOOR -->
    @if($currentFloor)
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8 space-y-8">
            <!-- Row Quick Creation Toolbar with Section Name Field -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200/90 p-4 sm:p-5">
                <form wire:submit="addRowWithSeats" class="flex flex-wrap items-end gap-3 sm:gap-4">
                    <!-- Section / Area Name (Hall, Kitchen, Cabin, Room 101, etc.) -->
                    <div class="w-full sm:w-44">
                        <label class="block text-[11px] font-bold text-indigo-700 uppercase tracking-wider mb-1">
                            Area / Section Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="sectionName" 
                            placeholder="e.g. Hall, Kitchen, Cabin" 
                            class="w-full rounded-xl bg-white border border-indigo-200 px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none shadow-2xs font-medium" />
                    </div>

                    <!-- Row Label -->
                    <div class="w-28 sm:w-32">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                            Row Label
                        </label>
                        <input 
                            type="text" 
                            wire:model="newRowLabel" 
                            placeholder="Row 1" 
                            class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <!-- Seats Count -->
                    <div class="w-24 sm:w-28">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                            Seats Count
                        </label>
                        <input 
                            type="number" 
                            wire:model="newRowSeatsCount" 
                            min="1" 
                            max="50"
                            class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <!-- Seat Prefix -->
                    <div class="w-28 sm:w-32">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                            Seat Prefix
                        </label>
                        <input 
                            type="text" 
                            wire:model="newRowSeatPrefix" 
                            placeholder="R1-" 
                            class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <!-- Unit Type -->
                    <div class="w-36">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                            Unit Type
                        </label>
                        <select 
                            wire:model="newRowType"
                            class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="seat">🪑 Seat (Library/Chair)</option>
                            <option value="desk">💻 Desk (Coworking)</option>
                            <option value="bed">🛏️ Bed (Hostel/PG/House)</option>
                            <option value="cabin">🚪 Cabin (Private)</option>
                        </select>
                    </div>

                    <!-- Add Button -->
                    <div class="flex-1 sm:flex-initial">
                        <button 
                            type="submit"
                            id="btn-add-row"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold text-xs shadow-xs hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Add Row to {{ $currentFloor->name }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Cinema Theater / Hall Stage Indicator -->
            <div class="relative py-4 text-center">
                <div class="mx-auto max-w-xl h-2.5 rounded-full bg-gradient-to-r from-transparent via-indigo-400 to-transparent shadow-xs"></div>
                <div class="mt-2 text-[11px] font-bold uppercase tracking-widest text-indigo-700 flex items-center justify-center gap-2">
                    <span>&mdash;&mdash;&mdash;</span>
                    <span>FRONT ENTRANCE / SCREEN / STAGE SIDE</span>
                    <span>&mdash;&mdash;&mdash;</span>
                </div>
            </div>

            <!-- Visual Seat Grid Grouped by Section (Hall, Kitchen, Cabin, etc.) -->
            @if($groupedRows->count() > 0)
                <div class="space-y-8">
                    @foreach($groupedRows as $sectionTitle => $rows)
                        <div 
                            wire:key="section-card-{{ Str::slug($sectionTitle) }}"
                            x-data="{
                                sortableInstance: null,
                                init() {
                                    this.setupSortable();
                                },
                                setupSortable() {
                                    if (!window.Sortable || !this.$refs.rowContainer) return;
                                    if (this.sortableInstance) {
                                        this.sortableInstance.destroy();
                                    }
                                    this.sortableInstance = new window.Sortable(this.$refs.rowContainer, {
                                        animation: 180,
                                        handle: '.row-drag-handle',
                                        ghostClass: 'sortable-ghost',
                                        chosenClass: 'sortable-chosen',
                                        dragClass: 'sortable-drag',
                                        onEnd: () => {
                                            let rowIds = Array.from(this.$refs.rowContainer.children)
                                                .map(el => el.getAttribute('data-row-id'))
                                                .filter(Boolean);
                                            if (rowIds.length > 0) {
                                                $wire.reorderRows(rowIds);
                                            }
                                        }
                                    });
                                }
                            }"
                            class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 sm:p-5 space-y-4"
                        >
                            <!-- Section Title Banner (Hall, Kitchen, etc.) -->
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                                        Area / Room: <span class="text-indigo-600">{{ $sectionTitle }}</span>
                                    </h3>
                                    <span class="text-[11px] text-slate-400 font-medium">({{ $rows->count() }} rows)</span>
                                </div>

                                <div class="text-[11px] text-slate-500 flex items-center gap-1 font-medium bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 2a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4z" />
                                    </svg>
                                    <span>Drag grip or use &uarr;&darr; to reorder</span>
                                </div>
                            </div>

                            <!-- Rows inside this section -->
                            <div x-ref="rowContainer" class="space-y-4 overflow-x-auto pb-2">
                                @foreach($rows as $row)
                                    <div 
                                        wire:key="seat-row-{{ $row->id }}" 
                                        data-row-id="{{ $row->id }}"
                                        class="flex items-center gap-3 min-w-max p-2 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 transition-all shadow-2xs group/row"
                                    >
                                        <!-- Drag Grip Handle -->
                                        <div 
                                            class="row-drag-handle cursor-grab active:cursor-grabbing p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors shrink-0 select-none" 
                                            title="Click & drag to reorder this row"
                                        >
                                            <svg class="w-4 h-4 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M7 2a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4z" />
                                            </svg>
                                        </div>

                                        <!-- Row Label Pill -->
                                        <div class="w-24 shrink-0 text-center">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold text-xs border border-slate-200 font-mono-code block truncate">
                                                {{ $row->row_label }}
                                            </span>
                                        </div>

                                        <!-- Seats in this row -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @foreach($row->seats as $seat)
                                                @if($seat->is_walkway)
                                                    <!-- Walking Aisle / Galli Slot -->
                                                    <button 
                                                        wire:click="toggleWalkway({{ $seat->id }})"
                                                        title="Walkway / Galli. Click to turn back into a seat."
                                                        class="w-12 h-12 rounded-xl border-2 border-dashed border-slate-300 bg-slate-100/70 hover:bg-slate-200 flex flex-col items-center justify-center text-slate-400 hover:text-indigo-600 transition-all cursor-pointer group">
                                                        <span class="text-[9px] uppercase font-bold tracking-tighter opacity-80 group-hover:opacity-100">Galli</span>
                                                        <span class="text-xs">··</span>
                                                    </button>
                                                @else
                                                    <!-- Active Seat / Desk / Bed Box -->
                                                    <div class="relative group">
                                                        <button 
                                                            wire:click="editSeat({{ $seat->id }})"
                                                            class="w-12 h-12 rounded-xl flex flex-col items-center justify-center transition-all cursor-pointer border shadow-2xs
                                                                @if($seat->status === 'available') 
                                                                    bg-white border-emerald-400 text-slate-900 hover:bg-emerald-50 hover:shadow-xs
                                                                @elseif($seat->status === 'occupied') 
                                                                    bg-rose-50 border-rose-300 text-rose-800 hover:bg-rose-100
                                                                @elseif($seat->status === 'reserved') 
                                                                    bg-amber-50 border-amber-300 text-amber-800 hover:bg-amber-100
                                                                @else 
                                                                    bg-slate-100 border-slate-300 text-slate-600
                                                                @endif">
                                                            <!-- Seat Top Accent -->
                                                            <div class="w-6 h-1 rounded-full mb-0.5
                                                                @if($seat->status === 'available') bg-emerald-500
                                                                @elseif($seat->status === 'occupied') bg-rose-500
                                                                @elseif($seat->status === 'reserved') bg-amber-500
                                                                @else bg-slate-400
                                                                @endif"></div>
                                                            
                                                             <!-- Seat Number -->
                                                            <span class="text-[11px] font-bold tracking-tight truncate max-w-[42px]">
                                                                {{ $seat->seat_number }}
                                                            </span>

                                                            <!-- Type icon -->
                                                            <span class="text-[9px] font-mono-code leading-none">
                                                                @if($seat->type === 'seat') 🪑
                                                                @elseif($seat->type === 'desk') 💻
                                                                @elseif($seat->type === 'bed') 🛏️
                                                                @else 🚪
                                                                @endif
                                                            </span>
                                                        </button>

                                                        <!-- Quick Hover Toggle to Turn into Walkway (Aisle) -->
                                                        <button 
                                                            wire:click.stop="toggleWalkway({{ $seat->id }})"
                                                            title="Make this an Aisle / Galli (Gap)"
                                                            class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-white text-slate-500 hover:text-amber-600 hover:bg-slate-100 border border-slate-300 shadow-xs flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                            &divide;
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Row Controls (Right Side) -->
                                        <div class="ml-auto shrink-0 flex items-center gap-1 pl-4 border-l border-slate-200">
                                            <!-- Move Row Up -->
                                            <button 
                                                wire:click="moveRowUp({{ $row->id }})"
                                                title="Move row up"
                                                class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </button>

                                            <!-- Move Row Down -->
                                            <button 
                                                wire:click="moveRowDown({{ $row->id }})"
                                                title="Move row down"
                                                class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <div class="h-4 w-px bg-slate-200 mx-0.5"></div>

                                            <!-- Add Seat to row -->
                                            <button 
                                                wire:click="addSeatToRow({{ $row->id }})"
                                                title="Add 1 seat to this row"
                                                class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-indigo-600 text-slate-600 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                                +
                                            </button>

                                            <!-- Remove last seat -->
                                            <button 
                                                wire:click="removeLastSeatFromRow({{ $row->id }})"
                                                title="Remove last seat from this row"
                                                class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-amber-600 text-slate-600 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                                -
                                            </button>

                                            <!-- Delete Row -->
                                            <button 
                                                wire:click="deleteRow({{ $row->id }})"
                                                wire:confirm="Delete this entire row and its seats?"
                                                title="Delete entire row"
                                                class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-rose-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Visual Grid Legend -->
                <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="font-semibold text-slate-700">Legend:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md bg-white border-2 border-emerald-400 inline-block"></span>
                            <span class="text-slate-700">Available</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md bg-rose-50 border-2 border-rose-400 inline-block"></span>
                            <span class="text-slate-700">Occupied</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md bg-amber-50 border-2 border-amber-400 inline-block"></span>
                            <span class="text-slate-700">Reserved</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md border-2 border-dashed border-slate-300 bg-slate-100 inline-block"></span>
                            <span class="text-slate-700">Aisle (Galli / Walkway)</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-indigo-700 font-medium">
                        💡 Tip: Click on any seat to edit details or toggle it as a walking aisle (galli)!
                    </div>
                </div>
            @else
                <!-- Empty Rows State -->
                <div class="text-center py-12 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 max-w-lg mx-auto">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center mx-auto mb-3 shadow-xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No Rows Added to {{ $currentFloor->name }}</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Type the area name (e.g. Hall, Kitchen, Cabin) and click <span class="font-semibold text-slate-700">+ Add Row to {{ $currentFloor->name }}</span> above!
                    </p>
                </div>
            @endif
        </div>
    @endif

    <!-- MODAL 1: Add Floor Modal -->
    @if($showAddFloorModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white border border-slate-200 p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Add Floor / Level</h3>
                <form wire:submit="addFloor" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Floor Name
                        </label>
                        <input 
                            type="text" 
                            wire:model="newFloorName" 
                            placeholder="e.g. Ground Floor, 1st Floor, Upper Deck" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 outline-none" />
                        @error('newFloorName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Level Type
                        </label>
                        <select 
                            wire:model="newFloorType"
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="floor">Floor (Standard Building)</option>
                            <option value="deck">Deck (Bus / Train / Coach)</option>
                            <option value="zone">Zone / Wing</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showAddFloorModal', false)" class="px-3.5 py-2 text-xs text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-xs">Add Floor</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Bulk Generator Modal (Rows A to E) -->
    @if($showBulkModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white border border-slate-200 p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Bulk Cinema Row Generator</h3>
                    <button wire:click="$set('showBulkModal', false)" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>

                <p class="mt-2 text-xs text-slate-500">
                    Generate multiple rows and seats simultaneously!
                </p>

                <form wire:submit="bulkGenerateRows" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Area / Section Name
                        </label>
                        <input 
                            type="text" 
                            wire:model="bulkSectionName" 
                            placeholder="e.g. Hall, Kitchen, Cabin" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Start Row Letter
                            </label>
                            <input 
                                type="text" 
                                wire:model="bulkStartLetter" 
                                maxlength="1"
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2 text-center font-bold text-slate-900 uppercase focus:bg-white focus:border-indigo-500 outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                End Row Letter
                            </label>
                            <input 
                                type="text" 
                                wire:model="bulkEndLetter" 
                                maxlength="1"
                                class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2 text-center font-bold text-slate-900 uppercase focus:bg-white focus:border-indigo-500 outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Seats Per Row
                        </label>
                        <input 
                            type="number" 
                            wire:model="bulkSeatsPerRow" 
                            min="1" 
                            max="50"
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Unit Type
                        </label>
                        <select 
                            wire:model="bulkSeatType"
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-4 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="seat">🪑 Seat (Library)</option>
                            <option value="desk">💻 Desk (Coworking)</option>
                            <option value="bed">🛏️ Bed (Hostel/PG/House)</option>
                            <option value="cabin">🚪 Cabin</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showBulkModal', false)" class="px-3.5 py-2 text-xs text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-semibold bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl hover:scale-[1.02] shadow-xs">
                            Generate Entire Layout &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Edit Seat Modal -->
    @if($editingSeatId)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-sm rounded-3xl bg-white border border-slate-200 p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Edit Seat #{{ $editSeatNumber }}</h3>
                    <button wire:click="closeSeatEdit" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>

                <form wire:submit="saveSeat" class="mt-4 space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Seat Label / Number
                        </label>
                        <input 
                            type="text" 
                            wire:model="editSeatNumber" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Status
                        </label>
                        <select 
                            wire:model="editSeatStatus"
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="available">🟢 Available</option>
                            <option value="occupied">🔴 Occupied</option>
                            <option value="reserved">🟡 Reserved</option>
                            <option value="maintenance">⚪ Maintenance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Unit Type
                        </label>
                        <select 
                            wire:model="editSeatType"
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="seat">🪑 Seat</option>
                            <option value="desk">💻 Desk</option>
                            <option value="bed">🛏️ Bed</option>
                            <option value="cabin">🚪 Cabin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Monthly Rate / Price (Optional)
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="editSeatPrice" 
                            placeholder="e.g. 1500" 
                            class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 outline-none" />
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" wire:click="closeSeatEdit" class="px-3.5 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-xs">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
