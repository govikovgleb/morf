<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Domain;

use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use HasUuid;

    protected static string $factory = \Database\Factories\UserFactory::class;

    protected $fillable = [
        'public_nickname',
        'email',
        'role',
        'auth_hash',
        'recovery_code_hash',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'auth_hash',
        'recovery_code_hash',
        'password',
        'remember_token',
    ];

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function getFilamentName(): string
    {
        return $this->public_nickname ?? 'Anonymous';
    }

    public function getNameAttribute(): string
    {
        return $this->public_nickname ?? 'Anonymous';
    }
}
