<?php

namespace App\Models;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property null|string $description
 * @property string $url
 */
class Changelog extends Model
{
    use HasFactory;
    use HasLocalTime;

    protected $fillable = [
        'title',
        'description',
        'url',
        'types',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'types' => 'array',
    ];
}
