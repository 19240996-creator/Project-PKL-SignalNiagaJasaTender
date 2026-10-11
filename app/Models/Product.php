<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'purchase_price',
        'selling_price',
        'minimum_stock',
        'is_active',
        'deletion_status',
        'deletion_reason',
        'deletion_requested_by',
        'deletion_requested_at',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'is_active' => 'boolean',
        'deletion_requested_at' => 'datetime',
    ];

    protected $appends = [
        'stock',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function tenderItems(): HasMany
    {
        return $this->hasMany(TenderItem::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getStockAttribute(): float
    {
        if ($this->relationLoaded('stockMovements')) {
            $in = (float) $this->stockMovements->where('movement_type', 'IN')->sum('quantity');
            $out = (float) $this->stockMovements->where('movement_type', 'OUT')->sum('quantity');
            $adj = (float) $this->stockMovements->where('movement_type', 'ADJUSTMENT')->sum('quantity');
            return (float) ($in - $out + $adj);
        }

        $in = (float) $this->stockMovements()->where('movement_type', 'IN')->sum('quantity');
        $out = (float) $this->stockMovements()->where('movement_type', 'OUT')->sum('quantity');
        $adj = (float) $this->stockMovements()->where('movement_type', 'ADJUSTMENT')->sum('quantity');
        return (float) ($in - $out + $adj);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ProductChangeRequest::class);
    }

    public function pendingAdjustment(): HasOne
    {
        return $this->hasOne(ProductChangeRequest::class)
            ->where('request_type', 'stock_adjustment')
            ->where('status', 'pending');
    }

    public function pendingEdit(): HasOne
    {
        return $this->hasOne(ProductChangeRequest::class)
            ->where('request_type', 'price_edit')
            ->where('status', 'pending');
    }
}
