<?php

namespace App\Models;

use Spatie\TranslationLoader\LanguageLine;

class Translation extends LanguageLine
{
    public $table = 'language_lines';
    
    protected $casts = [
        'text' => 'array',
        'is_system' => 'boolean',
    ];
}
