<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'phone', 'email', 'competencies', 'status', 'notes'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function availabilities(): HasMany { return $this->hasMany(TechnicianAvailability::class); }
    public function assignments(): HasMany { return $this->hasMany(ServiceAssignment::class); }
}
