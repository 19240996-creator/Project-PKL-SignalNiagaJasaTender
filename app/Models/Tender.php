<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'tender_number',
        'name',
        'source',
        'found_date',
        'deadline',
        'estimated_value',
        'bid_value',
        'status',
        'result',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'found_date' => 'date',
        'deadline' => 'date',
        'estimated_value' => 'decimal:2',
        'bid_value' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenderDocument::class);
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
