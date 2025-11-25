<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteConfig extends Model
{
    use HasFactory;

    // Because table is "routes_config" not "route_configs"
    protected $table = 'routes_config';

    protected $fillable = [
        'route_id',
        'name',
        'value',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
