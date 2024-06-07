<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasUser
{
    protected static function booted(): void
    {
        static::creating(function (Model $model): void {
            $model->user_id = Auth::id();
        });
    }
}
