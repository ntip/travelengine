<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasUuids;

    protected $fillable = ['ori', 'dst'];

    public $incrementing = false;
    protected $keyType = 'string';
    
    public function configs()
    {
        return $this->hasMany(RouteConfig::class, 'route_id');
    }


}
