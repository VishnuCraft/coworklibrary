<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Seat Booking Requests
                </h1>
                @if($pendingCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-black animate-pulse">
                        {{ $pendingCount }} Pending
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Review seat applications from students &amp; professionals. Approving marks the seat as occupied.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.seats') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200/90 text-slate-700 hover:text-slate-900 font-semibold text-xs shadow-2xs hover:shadow-xs transition-all">
                <span>View Seat Inventory</span>
            </a>
        </div>
    </div>

    <!-- Alert Message -->
    @if($feedbackMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs animate-in fade-in">
            <div class="flex items-center gap-2">
                <span class="text-emerald-600 font-black">✓</span>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button wire:click="$set('feedbackMessage', null)" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto">
            <button 
                wire:click="setFilter('pending')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0 flex items-center gap-1.5
                    {{ $filter === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>⏳ Pending</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white text-amber-800 font-extrabold">{{ $pendingCount }}</span>
                @endif
            </button>

            <button 
                wire:click="setFilter('approved')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0 flex items-center gap-1.5
                    {{ $filter === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>✅ Approved</span>
                <span class="text-[10px] opacity-80 font-mono">({{ $approvedCount }})</span>
            </button>

            <button 
                wire:click="setFilter('rejected')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0
                    {{ $filter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>❌ Rejected</span>
            </button>

            <button 
                wire:click="setFilter('all')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0
                    {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>All Requests ({{ $totalCount }})</span>
            </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Search applicant or seat..."
                class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Bulk Action Toolbar when Pending Filter is active or bookings selected -->
    @if($pendingCount > 0 && ($filter === 'pending' || $filter === 'all'))
        <div class="flex items-center justify-between p-3.5 px-5 bg-indigo-50/70 border border-indigo-200 rounded-2xl flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <button wire:click="selectAllPending" class="text-xs font-bold text-indigo-700 hover:text-indigo-900 cursor-pointer underline">
                    {{ count($selectedBookings) > 0 ? 'Deselect All' : 'Select All Pending (' . $pendingCount . ')' }}
                </button>
                @if(count($selectedBookings) > 0)
                    <span class="text-xs text-indigo-600 font-bold">
                        &bull; {{ count($selectedBookings) }} requests selected
                    </span>
                @endif
            </div>

            @if(count($selectedBookings) > 0)
                <button wire:click="approveSelected" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    ✅ Approve Selected ({{ count($selectedBookings) }})
                </button>
            @endif
        </div>
    @endif

    <!-- Booking Requests List / Cards -->
    @if($bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs hover:shadow-sm transition-all flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <!-- Left: Customer & Seat Info -->
                    <div class="flex items-start gap-4">
                        @if($booking->status === 'pending')
                            <div class="pt-3">
                                <input type="checkbox" wire:model.live="selectedBookings" value="{{ $booking->id }}" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer">
                            </div>
                        @endif

                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center shrink-0">
                            <span class="text-[9px] uppercase font-bold text-indigo-400">Seat</span>
                            <span class="text-sm font-black text-indigo-700 font-mono-code leading-none">{{ $booking->seat_number }}</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-extrabold text-slate-900">
                                    {{ $booking->user->name }}
                                </h3>

                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($booking->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($booking->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($booking->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-slate-100 text-slate-600 border border-slate-200 @endif">
                                    {{ $booking->status }}
                                </span>

                                <span class="text-[11px] text-slate-400">
                                    Applied {{ $booking->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="text-xs text-slate-600">
                                Property: <strong class="text-slate-800">{{ $booking->property->name }}</strong> &bull; Floor: <strong>{{ $booking->space->name }}</strong>
                            </div>

                            <!-- Contact Pills -->
                            <div class="flex items-center gap-3 pt-1 flex-wrap text-xs">
                                <span class="text-slate-500 font-mono-code">✉️ {{ $booking->user->email }}</span>

                                @if($booking->user->phone)
                                    <span class="text-slate-700 font-mono-code font-bold">📞 {{ $booking->user->phone }}</span>
                                @endif

                                @if($booking->user->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->user->whatsapp_number) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-[11px] transition-colors">
                                        <span>💬 WhatsApp: {{ $booking->user->whatsapp_number }} &nearr;</span>
                                    </a>
                                @endif
                            </div>

                            @if($booking->notes)
                                <div class="mt-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    💬 <strong>Applicant Note:</strong> "{{ $booking->notes }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Booking Meta & Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 shrink-0 lg:border-l lg:border-slate-100 lg:pl-6">
                        <div class="text-right sm:text-left space-y-0.5">
                            @if($booking->monthly_price)
                                <div class="text-sm font-extrabold text-slate-900">
                                    ₹{{ number_format((float) $booking->monthly_price, 2) }} <span class="text-[11px] font-normal text-slate-500">/mo</span>
                                </div>
                            @endif
                            @if($booking->shift_timing)
                                <div class="text-[11px] font-semibold text-slate-600">
                                    Slot: {{ $booking->shift_timing }}
                                </div>
                            @endif
                            @if($booking->start_date)
                                <div class="text-[11px] text-slate-400">
                                    Start: {{ $booking->start_date->format('d M, Y') }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            @if($booking->status === 'pending')
                                <button 
                                    wire:click="approveBooking({{ $booking->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <span wire:loading.remove wire:target="approveBooking({{ $booking->id }})">✓ Approve &amp; Mark Booked</span>
                                    <span wire:loading wire:target="approveBooking({{ $booking->id }})">Approving...</span>
                                </button>

                                <button 
                                    wire:click="rejectBooking({{ $booking->id }})"
                                    wire:confirm="Are you sure you want to decline this booking request?"
                                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 font-bold text-xs transition-colors cursor-pointer">
                                    Reject
                                </button>
                            @elseif($booking->status === 'approved')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                    <span>✓ Seat Occupied</span>
                                </div>
                            @elseif($booking->status === 'rejected')
                                <span class="text-xs text-rose-500 font-semibold">Declined</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="p-12 text-center rounded-2xl bg-white border border-slate-200">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                📬
            </div>
            <h3 class="text-base font-bold text-slate-900">No booking requests found</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                No requests currently match the <strong>{{ $filter }}</strong> filter. When students or professionals apply to book seats from your public listings, they will show up here.
            </p>
        </div>
    @endif
</div>
