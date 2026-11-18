<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'gender',
        'phone',
        'address',
        'is_member',
        'member_discount_percent',
    ];

    protected function casts(): array
    {
        return [
            'is_member' => 'boolean',
            'member_discount_percent' => 'integer',
        ];
    }

    public function isMember(): bool
    {
        return (bool) $this->is_member;
    }

    public function getDiscountPercent(): int
    {
        return $this->isMember() ? ($this->member_discount_percent ?: 10) : 0;
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}

