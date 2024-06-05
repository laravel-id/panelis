<?php

namespace App\Models\Misc;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'due_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Todo $todo): void {
            if (empty($todo->user_id)) {
                $todo->user_id = Auth::id();
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
