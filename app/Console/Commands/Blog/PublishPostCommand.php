<?php

namespace App\Console\Commands\Blog;

use App\Events\Blog\PostPublished;
use App\Models\Blog\Post;
use Illuminate\Console\Command;

class PublishPostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:publish-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Post::where('published_at', '<=', now())
            ->get()
            ->map(function (Post $post) {
                $post->fill(['is_visible' => true]);
                $post->save();

                event(new PostPublished($post));
            });
    }
}
