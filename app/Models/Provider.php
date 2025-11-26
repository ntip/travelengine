<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'country',
        'active',
    ];

    public function configs()
    {
        return $this->hasMany(ProviderConfig::class, 'provider_code', 'code');
    }

    public function configValue(string $key, $default = null)
    {
        $cfg = $this->configs->firstWhere('name', $key);

        return $cfg?->value ?? $default;
    }
}
