<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerDailySummary extends Model
{
    protected $fillable = [
        'worker_id',
        'summary_date',
        'provisional_earnings',
        'confirmed_earnings'
    ];

    protected $casts = [
        'provisional_earnings' => 'decimal:2',
        'confirmed_earnings' => 'decimal:2',
        'summary_date' => 'date'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}