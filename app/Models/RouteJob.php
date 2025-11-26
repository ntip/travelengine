<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RouteJob extends Model
{
    protected $fillable = [
        'route_id',
        'job_date',
        'status',
        'archived',
        'next_run_at',
        'last_hydrated_at',
    ];

    protected $casts = [
        'job_date'         => 'date',
        'archived'         => 'boolean',
        'next_run_at'      => 'datetime',
        'last_hydrated_at' => 'datetime',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('archived', false);
    }
}
