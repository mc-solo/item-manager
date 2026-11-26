<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_HARDWARE_TECHNICIAN = 'hardware_technician';

    public const ROLE_SOFTWARE_TECHNICIAN = 'software_technician';

    public const ROLE_RECEPTION = 'reception';

    public const ROLES = [self::ROLE_ADMIN, self::ROLE_HARDWARE_TECHNICIAN, self::ROLE_SOFTWARE_TECHNICIAN, self::ROLE_RECEPTION];

    public const TECHNICIAN_ROLES = [self::ROLE_HARDWARE_TECHNICIAN, self::ROLE_SOFTWARE_TECHNICIAN];

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_HARDWARE_TECHNICIAN => 'Hardware technician',
            self::ROLE_SOFTWARE_TECHNICIAN => 'Software technician',
            self::ROLE_RECEPTION => 'Reception',
            default => $role,
        };
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTechnician(): bool
    {
        return in_array($this->role, self::TECHNICIAN_ROLES, true);
    }

    public function isHardwareTechnician(): bool
    {
        return $this->role === self::ROLE_HARDWARE_TECHNICIAN;
    }

    public function isSoftwareTechnician(): bool
    {
        return $this->role === self::ROLE_SOFTWARE_TECHNICIAN;
    }

    public function isReception(): bool
    {
        return $this->role === self::ROLE_RECEPTION;
    }

    public function assignedDevices()
    {
        return $this->hasMany(Device::class, 'assigned_to');
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
