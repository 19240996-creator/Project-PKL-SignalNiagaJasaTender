<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'request_type',
        'proposed_data',
        'reason',
        'status',
        'requested_by',
        'requested_at',
        'actioned_by',
        'actioned_at',
        'rejection_notes',
    ];

    protected $casts = [
        'proposed_data' => 'array',
        'requested_at' => 'datetime',
        'actioned_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function actioner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isStockAdjustment(): bool
    {
        return $this->request_type === 'stock_adjustment';
    }

    public function isPriceEdit(): bool
    {
        return $this->request_type === 'price_edit';
    }
}
