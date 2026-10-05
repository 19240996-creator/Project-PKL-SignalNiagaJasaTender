<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_name',
        'customer_phone',
        'nama_barang',
        'kuantitas',
        'harga_satuan',
        'tender_id',
        'sale_date',
        'total_amount',
        'status',
        'approval_status',
        'approval_notes',
        'notes',
        'catatan_pengiriman',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'kuantitas' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
