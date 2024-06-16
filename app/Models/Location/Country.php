<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
