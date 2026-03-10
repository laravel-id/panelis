<?php

namespace Modules\Module\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Module\Database\Factories\ModuleFactory;

/**
 * @property string $name
 * @property bool $is_enabled
 */
#[UseFactory(ModuleFactory::class)]
class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_enabled',
        'is_builtin',
    ];

    protected $casts = [
        'is_enabled' => 'bool',
        'is_builtin' => 'bool',
    ];
}
