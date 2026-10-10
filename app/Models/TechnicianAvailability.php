<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianAvailability extends Model
{
    use HasFactory;

    protected $fillable = ['technician_id', 'available_from', 'available_until', 'status', 'notes'];
    protected $casts = ['available_from' => 'date', 'available_until' => 'date'];
    public function technician(): BelongsTo { return $this->belongsTo(Technician::class); }
}
