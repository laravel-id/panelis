<?php

namespace App\Filament\Resources\Blog\PostResource\Enums;

enum PostPermission: string
{
    case Browse = 'BrowsePostBlog';

    case Read = 'ReadPostBlog';

    case Edit = 'EditPostBlog';

    case Add = 'AddPostBlog';

    case Delete = 'DeletePostBlog';
}
