<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'user_id',
        'property_id',
        'space_id',
        'seat_id',
        'seat_number',
        'shift',
        'customer_name',
        'customer_phone',
        'parent_phone',
        'exam',
        'status',
        'start_date',
        'monthly_price',
        'shift_timing',
        'notes',
        'rejection_reason',
        'approved_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'monthly_price' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * @return BelongsTo<Seat, $this>
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
