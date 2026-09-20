<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderStatus extends Model
{
    protected $fillable = ['name', 'sort_order', 'is_terminal'];

    protected $casts = ['is_terminal' => 'boolean'];

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'status_id');
    }
}