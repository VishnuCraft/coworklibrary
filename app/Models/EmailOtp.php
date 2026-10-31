<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'otp',
        'name',
        'phone',
        'whatsapp_number',
        'password',
        'role',
        'expires_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
