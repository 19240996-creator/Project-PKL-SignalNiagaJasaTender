<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = ['service_job_id', 'product_id', 'item_name', 'quantity', 'unit', 'status', 'notes', 'requested_by'];
    protected $casts = ['quantity' => 'decimal:2'];
    public function job(): BelongsTo { return $this->belongsTo(ServiceJob::class, 'service_job_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
}
