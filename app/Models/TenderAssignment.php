<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'user_id',
        'technician_name',
        'role_or_competency',
        'contact_phone',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
