<?php

namespace App\Livewire\Vendor;

use App\Mail\BookingApprovedMail;
use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.vendor')]
#[Title('Booking Requests - Owner Portal')]
class BookingManager extends Component
{
    public string $filter = 'pending';

    public string $search = '';

    public ?string $feedbackMessage = null;

    /**
     * @var array<int, int>
     */
    public array $selectedBookings = [];

    public function setFilter(string $status): void
    {
        $this->filter = $status;
        $this->selectedBookings = [];
    }

    public function selectAllPending(): void
    {
        $pendingIds = Booking::whereHas('property', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('status', 'pending')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        if (count($this->selectedBookings) === count($pendingIds)) {
            $this->selectedBookings = [];
        } else {
            $this->selectedBookings = $pendingIds;
        }
    }

    public function approveSelected(): void
    {
        if (empty($this->selectedBookings)) {
            return;
        }

        $bookings = Booking::with(['user', 'property', 'space', 'seat'])
            ->whereHas('property', fn ($q) => $q->where('user_id', Auth::id()))
            ->whereIn('id', $this->selectedBookings)
            ->where('status', 'pending')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $booking->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            if ($booking->seat) {
                $booking->seat->update([
                    'status' => 'occupied',
                ]);
            }

            try {
                if ($booking->user && $booking->user->email) {
                    Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));
                }
            } catch (\Throwable $e) {
                Log::error('Failed sending bulk booking approval email: '.$e->getMessage());
            }

            $count++;
        }

        $this->selectedBookings = [];
        $this->feedbackMessage = "Successfully approved {$count} booking requests! Corresponding seats are now marked as occupied.";
    }

    public function approveBooking(int $bookingId): void
    {
        $booking = Booking::with(['user', 'property', 'space', 'seat'])
            ->whereHas('property', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('id', $bookingId)
            ->first();

        if (! $booking) {
            $this->feedbackMessage = 'Booking request not found.';

            return;
        }

        // Approve the booking
        $booking->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Mark the Seat as occupied
        if ($booking->seat) {
            $booking->seat->update([
                'status' => 'occupied',
            ]);
        }

        // Send confirmation email to the customer
        try {
            if ($booking->user && $booking->user->email) {
                Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed sending booking approval email: '.$e->getMessage());
        }

        $this->feedbackMessage = "Booking for Seat {$booking->seat_number} has been APPROVED! Seat is now marked as occupied in your inventory.";
    }

    public function rejectBooking(int $bookingId, string $reason = ''): void
    {
        $booking = Booking::with('seat')
            ->whereHas('property', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('id', $bookingId)
            ->first();

        if (! $booking) {
            $this->feedbackMessage = 'Booking request not found.';

            return;
        }

        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $reason ?: 'Declined by host',
        ]);

        // If seat was occupied by this booking, restore to available
        if ($booking->seat && $booking->seat->status === 'occupied') {
            $booking->seat->update(['status' => 'available']);
        }

        $this->feedbackMessage = "Booking request for Seat {$booking->seat_number} has been rejected.";
    }

    public function render(): View
    {
        $query = Booking::with(['user', 'property', 'space', 'seat'])
            ->whereHas('property', function ($q) {
                $q->where('user_id', Auth::id());
            });

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        if (! empty(trim($this->search))) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('seat_number', 'like', $term)
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('phone', 'like', $term);
                    })
                    ->orWhereHas('property', function ($pq) use ($term) {
                        $pq->where('name', 'like', $term);
                    });
            });
        }

        $bookings = $query->latest()->get();

        // Calculate counts
        $pendingCount = Booking::whereHas('property', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('status', 'pending')
            ->count();

        $approvedCount = Booking::whereHas('property', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('status', 'approved')
            ->count();

        $totalCount = Booking::whereHas('property', fn ($q) => $q->where('user_id', Auth::id()))
            ->count();

        return view('livewire.vendor.booking-manager', [
            'bookings' => $bookings,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
