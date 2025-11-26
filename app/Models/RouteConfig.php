<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteConfig extends Model
{
    use HasFactory;

    protected $table = 'routes_config';

    protected $fillable = [
        'route_id',   // UUID → routes.id
        'name',
        'value',
    ];

    public function route()
    {
        // route_id (uuid) → routes.id (uuid)
        return $this->belongsTo(Route::class, 'route_id');
    }
}
