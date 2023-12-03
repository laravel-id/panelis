<?php

namespace App\Models\Blog;

use App\Models\Traits\HasLocalTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $local_updated_at
 * @property string $local_created_at
 * @property string $local_deleted_at
 * @property null|string $description
 */
class Category extends Model
{
    use HasFactory;
    use HasLocalTime;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    protected $table = 'blog_categories';

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'blog_category_post');
    }
}
