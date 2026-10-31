<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  array<int, Booking>  $allBookings
     */
    public function __construct(
        public Booking $booking,
        public array $allBookings = []
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = count($this->allBookings);

        if ($count > 1) {
            $seatNumbers = collect($this->allBookings)->pluck('seat_number')->implode(', ');

            return new Envelope(
                subject: "New Booking Request ({$count} Seats): {$seatNumbers} on {$this->booking->space->name} - {$this->booking->property->name}",
            );
        }

        return new Envelope(
            subject: "New Booking Request: Seat {$this->booking->seat_number} on {$this->booking->space->name} - {$this->booking->property->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-booking-request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
