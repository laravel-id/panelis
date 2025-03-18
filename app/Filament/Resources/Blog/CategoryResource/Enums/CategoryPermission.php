<?php

namespace App\Filament\Resources\Blog\CategoryResource\Enums;

enum CategoryPermission: string
{
    case Browse = 'BrowseCategoryBlog';

    case Read = 'ReadCategoryBlog';

    case Edit = 'EditCategoryBlog';

    case Add = 'AddCategoryBlog';

    case Delete = 'DeleteCategoryBlog';
}
