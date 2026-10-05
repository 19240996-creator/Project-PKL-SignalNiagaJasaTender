<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'client_id',
        'klien',
        'job_number',
        'name',
        'biaya',
        'start_date',
        'end_date',
        'status',
        'approval_status',
        'approval_notes',
        'progress',
        'notes',
        'deskripsi_pekerjaan',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'biaya' => 'decimal:2',
        'progress' => 'integer',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTotalBilledAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', '!=', 'Cancelled')
            ->sum('subtotal');
    }

    public function calculateProgress(): int
    {
        if ($this->status === 'Selesai') {
            return 100;
        }

        $contractValue = (float) ($this->contract?->contract_value ?? 0);
        if ($contractValue > 0) {
            $totalBilled = $this->total_billed;
            return min(100, (int) round(($totalBilled / $contractValue) * 100));
        }

        return (int) ($this->progress ?? 0);
    }

    public function syncProgress(): void
    {
        $calculated = $this->calculateProgress();
        $updates = ['progress' => $calculated];

        if ($calculated >= 100 && $this->status !== 'Selesai') {
            $updates['status'] = 'Selesai';
        }

        $this->update($updates);
    }
}
