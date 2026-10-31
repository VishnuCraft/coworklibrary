<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'parent_id',
        'name',
        'is_ac',
        'type',
        'sort_order',
        'aisle_position',
        'description',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_ac' => 'boolean',
        'aisle_position' => 'integer',
        'sort_order' => 'integer',
    ];

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
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Space::class, 'parent_id');
    }

    /**
     * @return HasMany<Space, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Space::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<SeatRow, $this>
     */
    public function seatRows(): HasMany
    {
        return $this->hasMany(SeatRow::class)->orderBy('row_order');
    }

    /**
     * @return HasMany<Seat, $this>
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
