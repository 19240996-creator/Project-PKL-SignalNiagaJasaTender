<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['service_job_id', 'technician_id', 'start_date', 'end_date', 'status', 'notes'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];
    public function job(): BelongsTo { return $this->belongsTo(ServiceJob::class, 'service_job_id'); }
    public function technician(): BelongsTo { return $this->belongsTo(Technician::class); }
}
