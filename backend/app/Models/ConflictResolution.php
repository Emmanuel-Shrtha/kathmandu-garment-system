<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConflictResolution extends Model
{
    protected $fillable = [
        'bundle_id',
        'resolved_by_user_id',
        'action',
        'notes',
        'before_snapshot',
        'after_snapshot'
    ];

    protected $casts = [
        'before_snapshot' => 'array',
        'after_snapshot' => 'array',
    ];
}