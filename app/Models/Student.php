<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'joined_on' => 'date',
        'paid_until' => 'date',
        'left_on' => 'date',
        'monthly_fee' => 'float',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getDaysLeftAttribute(): int
    {
        if (! $this->paid_until) {
            return 0;
        }

        return (int) Carbon::today()->diffInDays(Carbon::parse($this->paid_until), false);
    }

    public function getDueStageAttribute(): string
    {
        if ($this->status === 'left') {
            return 'left';
        }

        $d = $this->days_left;
        if ($d < 0) {
            return 'over';
        }
        if ($d === 0) {
            return 'today';
        }
        if ($d === 1) {
            return 'half';
        }
        if ($d <= 3) {
            return 'soon';
        }

        return 'ok';
    }

    public function getPendingFeeAttribute(): float
    {
        if ($this->status === 'left') {
            return 0;
        }

        $d = $this->days_left;
        if ($d > 0) {
            return 0;
        }

        $cycles = $d < 0 ? (int) floor((-$d) / 30) + 1 : 1;

        return (float) ($this->monthly_fee * $cycles);
    }

    public function getWaLink(bool $toParent = false): string
    {
        $targetPhone = $toParent ? ($this->parent_phone ?? $this->phone) : $this->phone;
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $targetPhone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91'.$cleanPhone;
        }

        $libraryName = $this->property->name ?? 'CoworkLibrary';
        $upiId = $this->property->upi_id ?? '';
        $firstName = explode(' ', $this->name)[0] ?? $this->name;
        $amt = $this->pending_fee > 0 ? $this->pending_fee : $this->monthly_fee;
        $d = $this->days_left;

        $dateFormatted = $this->paid_until ? $this->paid_until->format('d M') : 'due date';
        $when = $d < 0 ? "was due on {$dateFormatted}" : ($d === 0 ? 'is due today' : "is due on {$dateFormatted}");

        if ($toParent) {
            $who = "Hello, this is {$libraryName}. {$firstName}'s library fee of ₹{$amt} {$when}.";
        } else {
            $who = "Hi {$firstName}, your library fee of ₹{$amt} at {$libraryName} {$when}.";
        }

        $msg = $who;
        if ($upiId) {
            $msg .= "\nPay by UPI to: {$upiId} (amount ₹{$amt})";
        }
        $msg .= "\nPlease share the payment screenshot after paying. Thank you!";

        return 'https://wa.me/'.$cleanPhone.'?text='.rawurlencode($msg);
    }
}
