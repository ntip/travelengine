<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapeLog extends Model
{
    use HasFactory;

    protected $table = 'scrape_logs';

    protected $fillable = [
        'scrape_id',
        'route_job_id',
        'content',
        'scrape_response_raw',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function scrape()
    {
        return $this->belongsTo(Scrape::class, 'scrape_id');
    }

    public function job()
    {
        return $this->belongsTo(RouteJob::class, 'route_job_id');
    }
}
