<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderConfig extends Model
{
    use HasFactory;

    protected $table = 'provider_configs';

    protected $fillable = [
        'provider_code',
        'name',
        'value',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_code', 'code');
    }
}
