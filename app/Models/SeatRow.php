<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatRow extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'space_id',
        'section_name',
        'row_label',
        'row_order',
        'total_columns',
    ];

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * @return HasMany<Seat, $this>
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class)->orderBy('column_index');
    }
}
