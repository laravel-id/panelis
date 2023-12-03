<?php

namespace App\Models\Blog;

use App\Models\Traits\HasLocalTime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use HasLocalTime;
    use SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'image_location',
        'image',
        'is_visible',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'options' => 'json',
        'published_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_category_post');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
