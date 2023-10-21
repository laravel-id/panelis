<?php

namespace App\Models\Traits;

use App\Models\Misc\Todo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasTodos
{
    public function todos(): BelongsToMany
    {
        return $this->belongsToMany(Todo::class);
    }
}
