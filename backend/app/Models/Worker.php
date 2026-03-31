<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'biometric_pin',
        'full_name',
        'line',
        'active',
        'joined_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'joined_at' => 'date',
    ];

    public function claims()
    {
        return $this->hasMany(WorkerClaim::class);
    }
}