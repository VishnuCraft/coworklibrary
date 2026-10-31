<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'whatsapp_number', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'customer',
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Determine if user has administrator authority.
     */
    public function isAdministrator(): bool
    {
        return in_array($this->role, ['administrator', 'super_admin', 'admin'], true);
    }

    /**
     * Backward-compatible alias for isAdministrator.
     */
    public function isAdmin(): bool
    {
        return $this->isAdministrator();
    }

    /**
     * Kept for backwards compatibility.
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdministrator();
    }

    /**
     * Determine if user is a vendor / property owner.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor' || $this->properties()->exists() || $this->isAdministrator();
    }

    /**
     * Determine if user is a space owner.
     */
    public function isOwner(): bool
    {
        return $this->isVendor();
    }

    /**
     * Determine if user is a regular customer without any listed property.
     */
    public function isCustomer(): bool
    {
        return ! $this->isOwner();
    }

    /**
     * @return HasMany<Property, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
