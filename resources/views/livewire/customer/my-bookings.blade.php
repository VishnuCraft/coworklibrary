<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Student Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">🎓</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    My Student Dashboard
                </h1>
            </div>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Track your desk booking requests, allocated seats, shifts, and library fee status.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Browse Libraries</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1 px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-200 font-bold text-xs transition-all cursor-pointer">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    @if($feedbackMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span>{{ $feedbackMessage }}</span>
            <button wire:click="$set('feedbackMessage', null)" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Bookings Cards -->
    @if($bookings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 flex flex-col justify-between space-y-5 hover:shadow-md transition-shadow">
                    <div>
                        <!-- Status and Date -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider
                                @if($booking->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($booking->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($booking->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200
                                @else bg-slate-100 text-slate-600 border border-slate-200 @endif">
                                
                                @if($booking->status === 'approved')
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Seat Confirmed & Booked</span>
                                @elseif($booking->status === 'pending')
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Pending Owner Approval</span>
                                @elseif($booking->status === 'rejected')
                                    <span>Declined</span>
                                @else
                                    <span>Cancelled</span>
                                @endif
                            </span>

                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $booking->created_at->format('d M') }}
                            </span>
                        </div>

                        <!-- Property Title & Seat -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 line-clamp-1">
                                    {{ $booking->property->name ?? 'Study Library' }}
                                </h3>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $booking->property->area ?? '' }}{{ $booking->property->city ? ', '.$booking->property->city : '' }}
                                </div>
                            </div>

                            <div class="px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 text-center shrink-0">
                                <div class="text-[9px] uppercase font-bold tracking-wider text-indigo-400">Seat</div>
                                <div class="text-sm font-black font-mono leading-none mt-0.5">{{ $booking->seat_number }}</div>
                            </div>
                        </div>

                        <!-- Details Row -->
                        <div class="mt-4 pt-3.5 border-t border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Shift:</span>
                                <span class="font-bold text-slate-800 capitalize">{{ $booking->shift ?? 'Morning' }} shift</span>
                            </div>

                            @if($booking->exam)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Exam / Goal:</span>
                                    <span class="font-bold text-indigo-600">{{ $booking->exam }}</span>
                                </div>
                            @endif

                            @if($booking->monthly_price)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Monthly Fee:</span>
                                    <span class="font-extrabold text-slate-900">₹{{ number_format((float) $booking->monthly_price) }}/mo</span>
                                </div>
                            @endif

                            @if($booking->start_date)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Start Date:</span>
                                    <span class="font-semibold text-slate-700">{{ $booking->start_date->format('d M, Y') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($booking->status === 'approved')
                            <div class="mt-3.5 p-3 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-xs text-emerald-950">
                                <div class="font-extrabold flex items-center gap-1.5 text-emerald-800">
                                    <span>✅</span>
                                    <span>Seat Confirmed & Booked!</span>
                                </div>
                                <p class="mt-1 text-[11px] text-emerald-700 leading-relaxed">
                                    The library owner has approved your desk. Your seat is confirmed for your shift. Fee payment can be paid directly at the library.
                                </p>
                            </div>
                        @endif

                        <!-- Contact / Note -->
                        @if($booking->property && $booking->property->phone)
                            <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Library Contact</div>
                                    <div class="font-bold text-slate-900 mt-0.5">
                                        {{ $booking->property->phone }}
                                    </div>
                                </div>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $booking->property->phone);
                                    if(strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
                                    $msg = "Hi, I have applied for Seat {$booking->seat_number} ({$booking->shift} shift) at {$booking->property->name}.";
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($msg) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition-colors">
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="pt-2">
                        @if($booking->status === 'pending')
                            <button 
                                wire:click="cancelBooking({{ $booking->id }})"
                                wire:confirm="Are you sure you want to cancel this booking request?"
                                class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 text-xs font-bold transition-colors cursor-pointer text-center">
                                Cancel Request
                            </button>
                        @else
                            <a href="{{ route('home') }}" 
                               class="w-full inline-block py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors text-center">
                                View Library &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 max-w-lg mx-auto shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 text-3xl">
                🪑
            </div>
            <h3 class="text-lg font-bold text-slate-900">No seat requests yet</h3>
            <p class="text-xs text-slate-500 mt-1">
                You haven't requested any library seats yet. Browse available study libraries, choose your shift, and pick a seat.
            </p>
            <a href="{{ route('home') }}" class="mt-5 inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all">
                Browse Study Libraries &rarr;
            </a>
        </div>
    @endif
</div>
