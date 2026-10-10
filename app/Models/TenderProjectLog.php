<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderProjectLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'log_date',
        'progress_percentage',
        'activity_description',
        'obstacles',
        'solutions',
        'actual_cost_spent',
        'documentation_file',
        'logged_by',
    ];

    protected $casts = [
        'log_date' => 'date',
        'progress_percentage' => 'integer',
        'actual_cost_spent' => 'decimal:2',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
