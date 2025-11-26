<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrape extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_job_id',
        'provider_code',
        'provider_url',
        'status',
        'started_at',
        'finished_at',
        'attempt',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'attempt' => 'integer',
    ];

    public function job()
    {
        return $this->belongsTo(RouteJob::class, 'route_job_id');
    }

    public function logs()
    {
        return $this->hasMany(ScrapeLog::class, 'scrape_id');
    }
}
