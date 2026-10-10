<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderRabItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'category',
        'product_id',
        'item_name',
        'quantity',
        'unit',
        'unit_cost',
        'unit_price',
        'subtotal_cost',
        'subtotal_price',
        'allocated_quantity',
        'used_quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal_cost' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
        'allocated_quantity' => 'decimal:2',
        'used_quantity' => 'decimal:2',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Hitung kekurangan stok jika kategori barang dan terhubung dengan master produk modul Dagang
     */
    public function getDeficitAttribute(): float
    {
        if ($this->category !== 'barang' || !$this->product_id || !$this->product) {
            return 0.0;
        }

        $availableStock = (float) $this->product->stock;
        $neededQuantity = (float) $this->quantity;

        return max(0.0, $neededQuantity - $availableStock);
    }

    public function getHasDeficitAttribute(): bool
    {
        return $this->deficit > 0;
    }

    /**
     * Sisa alokasi barang yang belum terpakai di lapangan
     */
    public function getRemainingAllocatedAttribute(): float
    {
        return max(0.0, (float) $this->allocated_quantity - (float) $this->used_quantity);
    }
}
