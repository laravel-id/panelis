<?php

use App\Models\User;
use Panelis\User\Models\Permission;
use Panelis\User\Models\Role;

return [
    'models' => [
        'user' => User::class,
        'role' => Role::class,
        'permission' => Permission::class,
    ],
];
