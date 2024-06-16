<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
    ];

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class);
    }

    public static function options(): array
    {
        return self::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }
}
