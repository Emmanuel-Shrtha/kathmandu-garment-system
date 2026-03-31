<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleException extends Model
{
    protected $fillable = [
        'bundle_id',
        'type',           // lost, damaged, merged, split, qr_torn
        'reported_by_user_id',
        'resolved',
        'resolution_notes',
        'photo_url'
    ];

    protected $casts = [
        'resolved' => 'boolean'
    ];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }
}