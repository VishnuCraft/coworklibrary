<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'address',
        'area',
        'city',
        'phone',
        'upi_id',
        'open_time',
        'close_time',
        'shifts',
        'facilities',
        'description',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'shifts' => 'array',
        'facilities' => 'array',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Space, $this>
     */
    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class)->orderBy('sort_order');
    }

    /**
     * Top-level spaces (e.g., floors, halls).
     *
     * @return HasMany<Space, $this>
     */
    public function topLevelSpaces(): HasMany
    {
        return $this->hasMany(Space::class)->whereNull('parent_id')->orderBy('sort_order');
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
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getMinPriceAttribute(): int
    {
        if (! empty($this->shifts)) {
            $prices = [];
            foreach ($this->shifts as $shift) {
                if (isset($shift['price']) && $shift['price'] > 0) {
                    $prices[] = (int) $shift['price'];
                }
            }
            if (! empty($prices)) {
                return min($prices);
            }
        }

        return 700;
    }

    public function getFreeSeatsCount(string $shift = 'morning'): int
    {
        $count = 0;
        foreach ($this->spaces as $space) {
            foreach ($space->seats as $seat) {
                if (! $seat->is_walkway && $seat->isFreeForShift($shift)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
