<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $fillable = [
        'bundle_qty',
        'qr_code',
        'current_holder_worker_id',
        'last_scanned_at',
        'parent_bundle_id',
        'rework_level'
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
    ];

    public function claims()
    {
        return $this->hasMany(WorkerClaim::class);
    }

    public function currentHolder()
    {
        return $this->belongsTo(Worker::class, 'current_holder_worker_id');
    }
}