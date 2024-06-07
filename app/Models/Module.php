<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property bool $is_enabled
 */
class Module extends Model
{
    use HasFactory;

    protected $casts = [
        'is_enabled' => 'bool',
        'is_builtin' => 'bool',
    ];
}
