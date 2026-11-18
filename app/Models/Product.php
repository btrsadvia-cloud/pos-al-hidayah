<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'image',
        'stock',
        'min_stock',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'data:image') || str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the tiered price and tier name for a given quantity.
     * Evaluates highest matching min_qty tier (super_wholesale > wholesale > retail).
     */
    public function getPriceForQty(int $qty): array
    {
        $prices = $this->prices->sortByDesc('min_qty');

        foreach ($prices as $tierPrice) {
            if ($qty >= $tierPrice->min_qty) {
                return [
                    'price' => (int) $tierPrice->price,
                    'tier' => $tierPrice->tier,
                    'min_qty' => $tierPrice->min_qty,
                ];
            }
        }

        // Fallback to lowest min_qty price or retail tier
        $retail = $this->prices->firstWhere('tier', 'retail') ?? $prices->last();

        return [
            'price' => $retail ? (int) $retail->price : 0,
            'tier' => $retail ? $retail->tier : 'retail',
            'min_qty' => $retail ? $retail->min_qty : 1,
        ];
    }

    public function retailPrice(): ?int
    {
        $price = $this->prices->firstWhere('tier', 'retail');
        return $price ? (int) $price->price : null;
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function latestCost(): ?int
    {
        $movement = $this->stockMovements()
            ->where('type', 'in')
            ->whereNotNull('unit_cost')
            ->latest('id')
            ->first();

        return $movement ? (int) $movement->unit_cost : null;
    }
}

