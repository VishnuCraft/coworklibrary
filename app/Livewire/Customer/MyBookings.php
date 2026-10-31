<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('My Seat Bookings - CoworkLibrary')]
class MyBookings extends Component
{
    public ?string $feedbackMessage = null;

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('id', $bookingId)
            ->where('status', 'pending')
            ->first();

        if ($booking) {
            $booking->update(['status' => 'cancelled']);
            $this->feedbackMessage = "Booking request for Seat {$booking->seat_number} has been cancelled.";
        }
    }

    public function render(): View
    {
        $bookings = Booking::with(['property.user', 'space', 'seat'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.customer.my-bookings', [
            'bookings' => $bookings,
        ]);
    }
}
