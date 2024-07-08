<?php

namespace App\Models\Location;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;
    use HasLocalTime;

    protected $fillable = [
        'alpha2',
        'alpha3',
        'un_code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
