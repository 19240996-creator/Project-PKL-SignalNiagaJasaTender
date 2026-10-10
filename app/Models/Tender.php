<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasMany as HasManyRelation;

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
        'metode_penanganan',
        'nama_vendor_relasi',
        'alasan_metode',
        'status',
        'approval_status',
        'submission_status',
        'approval_notes',
        'revisi_notes',
        'rab_status',
        'rab_notes',
        'rab_submitted_at',
        'rab_approved_by',
        'rab_approved_at',
        'project_status',
        'project_progress',
        'project_location',
        'project_pic',
        'project_start_date',
        'project_end_date',
        'actual_cost',
        'vendor_progress',
        'vendor_notes',
        'result',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'deletion_status',
        'deletion_reason',
        'deletion_requested_by',
        'deletion_requested_at',
    ];

    protected $casts = [
        'found_date' => 'date',
        'deadline' => 'date',
        'estimated_value' => 'decimal:2',
        'bid_value' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'project_start_date' => 'date',
        'project_end_date' => 'date',
        'rab_submitted_at' => 'datetime',
        'rab_approved_at' => 'datetime',
        'approved_at' => 'datetime',
        'deletion_requested_at' => 'datetime',
        'project_progress' => 'integer',
        'vendor_progress' => 'integer',
    ];

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

    public function rabApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rab_approved_by');
    }

    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
    }

    public function isPendingDeletion(): bool
    {
        return $this->deletion_status === 'pending_deletion';
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenderDocument::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TenderItem::class);
    }

    public function rabItems(): HasMany
    {
        return $this->hasMany(TenderRabItem::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TenderAssignment::class);
    }

    public function projectLogs(): HasMany
    {
        return $this->hasMany(TenderProjectLog::class);
    }

    public function approvalHistories(): HasMany
    {
        return $this->hasMany(TenderApprovalHistory::class)->orderBy('created_at', 'desc');
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function tenderStatus(): BelongsTo
    {
        return $this->belongsTo(TenderStatus::class, 'status_id');
    }

    public function evaluations(): HasManyRelation
    {
        return $this->hasMany(TenderEvaluation::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Total Biaya Modal / HPP RAB
     */
    public function getTotalRabCostAttribute(): float
    {
        return (float) $this->rabItems->sum('subtotal_cost');
    }

    /**
     * Total Nilai Penawaran Berdasarkan Rincian RAB
     */
    public function getTotalRabPriceAttribute(): float
    {
        return (float) $this->rabItems->sum('subtotal_price');
    }

    /**
     * Estimasi Keuntungan (Gross Profit)
     * Nilai Penawaran Tender - Total HPP RAB
     */
    public function getEstimatedProfitAttribute(): float
    {
        $offerValue = (float) ($this->bid_value > 0 ? $this->bid_value : $this->total_rab_price);
        return $offerValue - $this->total_rab_cost;
    }

    /**
     * Margin Keuntungan (%)
     */
    public function getProfitMarginPercentageAttribute(): float
    {
        $offerValue = (float) ($this->bid_value > 0 ? $this->bid_value : $this->total_rab_price);
        if ($offerValue <= 0) {
            return 0.0;
        }

        return round(($this->estimated_profit / $offerValue) * 100, 2);
    }

    /**
     * Total item barang di RAB yang mengalami defisit stok di gudang (Modul Dagang)
     */
    public function getDeficitItemsCountAttribute(): int
    {
        return $this->rabItems->filter(fn ($item) => $item->has_deficit)->count();
    }

    /**
     * Status kelayakan pelaksanaan proyek (Internal vs Vendor)
     */
    public function getExecutionRecommendationAttribute(): array
    {
        $hasDeficit = $this->deficit_items_count > 0;
        $hasTechs = $this->assignments->count() > 0;

        if (!$hasDeficit && ($hasTechs || $this->metode_penanganan === 'internal')) {
            return [
                'type' => 'internal',
                'title' => 'Rekomendasi: Pelaksanaan Internal (Sendiri)',
                'badge_class' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'description' => 'Ketersediaan material di gudang mencukupi dan kapasitas tim teknis internal memadai untuk menyelesaikan pekerjaan.',
            ];
        }

        return [
            'type' => 'vendor_or_procurement',
            'title' => 'Rekomendasi: Opsi Tambahan / Vendor Relasi',
            'badge_class' => 'bg-amber-50 text-amber-800 border-amber-200',
            'description' => $hasDeficit
                ? "Terdapat {$this->deficit_items_count} item barang dengan stok gudang tidak mencukupi. Pertimbangkan pengadaan tambahan melalui Modul Dagang atau pelimpahan ke Vendor Relasi."
                : 'Pertimbangkan penugasan personil teknisi tambahan atau pelimpahan ke Vendor Relasi untuk efisiensi jadwal.',
        ];
    }
}
