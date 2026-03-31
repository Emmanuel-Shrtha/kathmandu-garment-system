<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerClaim extends Model
{
    protected $fillable = [
        'bundle_id',
        'worker_id',
        'client_uuid',
        'claimed_qty',
        'passed_qty',
        'wasted_qty',
        'repaired_qty',
        'status'
    ];

    protected $casts = [
        'claimed_qty' => 'integer',
        'passed_qty' => 'integer',
        'wasted_qty' => 'integer',
        'repaired_qty' => 'integer',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }
}