<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderEvaluation extends Model
{
    protected $fillable = ['tender_id', 'evaluator_id', 'score', 'decision', 'notes', 'evaluated_at'];

    protected $casts = ['score' => 'decimal:2', 'evaluated_at' => 'datetime'];

    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function evaluator(): BelongsTo { return $this->belongsTo(User::class, 'evaluator_id'); }
}