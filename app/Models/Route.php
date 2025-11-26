<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Route extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['ori', 'dst'];
    public $incrementing = false;
    protected $keyType = 'string';

    // Existing:
    public function configs()
    {
        // FK: routes_config.route_id → routes.id (uuid)
        return $this->hasMany(RouteConfig::class, 'route_id');
    }

    /**
     * Generic getter for a config value on this route.
     */
    public function configValue(string $key, $default = null)
    {
        // Uses loaded relation if present, otherwise lazy loads.
        $cfg = $this->configs->firstWhere('name', $key);

        return $cfg?->value ?? $default;
    }

    /**
     * Number of days ahead we should generate jobs for this route.
     */
    public function daysAhead(): int
    {
        return (int) $this->configValue('days_ahead', 0);
    }

    /**
     * Number of days before departure that we should keep rehydrating successful jobs.
     */
    public function daysHydrate(): int
    {
        return (int) $this->configValue('days_hydrate', 0);
    }

    /**
     * Relationship to route_jobs table.
     */
    public function jobs()
    {
        return $this->hasMany(RouteJob::class, 'route_id');
    }
}
