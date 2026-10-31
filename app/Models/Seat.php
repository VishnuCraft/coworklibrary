<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'space_id',
        'seat_row_id',
        'seat_number',
        'column_index',
        'type',
        'is_walkway',
        'status',
        'price',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_walkway' => 'boolean',
        'column_index' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * @return BelongsTo<SeatRow, $this>
     */
    public function seatRow(): BelongsTo
    {
        return $this->belongsTo(SeatRow::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Check if seat is free for a given shift.
     * Rule: Free for 'full' only if empty in all shifts.
     * Free for 'morning' if nobody holds 'morning' or 'full'.
     * Free for 'evening' if nobody holds 'evening' or 'full'.
     */
    public function isFreeForShift(string $shift): bool
    {
        if ($this->is_walkway) {
            return false;
        }

        $active = $this->students->filter(fn ($s) => $s->status !== 'left');
        if ($shift === 'full') {
            return $active->isEmpty();
        }

        return ! $active->contains(fn ($s) => $s->shift === $shift || $s->shift === 'full');
    }

    /**
     * Get active student occupying this seat for a shift.
     */
    public function getStudentForShift(string $shift): ?Student
    {
        $active = $this->students->filter(fn ($s) => $s->status !== 'left');
        $exact = $active->firstWhere('shift', $shift);
        if ($exact) {
            return $exact;
        }

        return $active->firstWhere('shift', 'full');
    }
}
