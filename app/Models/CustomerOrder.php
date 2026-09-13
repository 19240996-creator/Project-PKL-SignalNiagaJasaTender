<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerOrder extends Model
{
    protected $guarded = [];
    protected $casts = ['items' => 'array', 'order_date' => 'date', 'total_amount' => 'decimal:2'];
    public function quotation(): BelongsTo { return $this->belongsTo(SalesQuotation::class, 'sales_quotation_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
