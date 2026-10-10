<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderApprovalHistory extends Model
{
    use HasFactory;

    protected $table = 'tender_approval_history';

    protected $fillable = [
        'tender_id',
        'module_type',
        'action',
        'user_id',
        'notes',
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
