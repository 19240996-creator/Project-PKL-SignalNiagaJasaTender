<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'competencies',
        'status',
        'notes',
        'deletion_status',
        'deletion_reason',
        'deletion_requested_by',
        'deletion_requested_at',
    ];

    protected $casts = [
        'deletion_requested_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function availabilities(): HasMany { return $this->hasMany(TechnicianAvailability::class); }
    public function assignments(): HasMany { return $this->hasMany(ServiceAssignment::class); }

    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
    }

    public function isPendingDeletion(): bool
    {
        return $this->deletion_status === 'pending_deletion';
    }
}
