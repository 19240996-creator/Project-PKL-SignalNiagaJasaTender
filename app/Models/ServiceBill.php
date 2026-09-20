<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBill extends Model
{
    protected $fillable = ['service_job_id', 'bill_number', 'bill_date', 'due_date', 'subtotal', 'tax_amount', 'total_amount', 'status', 'notes', 'created_by'];

    protected $casts = ['bill_date' => 'date', 'due_date' => 'date', 'subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2'];

    public function serviceJob(): BelongsTo { return $this->belongsTo(ServiceJob::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}