<?php

namespace App\Models\Location;

use App\Models\Profile;
use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;
    use HasLocalTime;

    protected $fillable = [
        'region_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => true,
    ];

    protected $guarded = [
        'id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
