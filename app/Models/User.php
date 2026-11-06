<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'password',
        'level',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->level === 1 && $this->is_active;
    }

    public function isCashier(): bool
    {
        return $this->level === 2 && $this->is_active;
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function currentShift(): ?Shift
    {
        return $this->shifts()
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();
    }

    public function hasOpenShift(): bool
    {
        return $this->currentShift() !== null;
    }
}
