<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'same_day', 'start_at', 'end_at', 'service_type', 'shift', 'meta',
    ];

    protected $casts = [
        'same_day'  => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'meta'      => 'array',
    ];

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'report_user');
    }

    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
