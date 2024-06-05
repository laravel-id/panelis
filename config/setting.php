<?php

return [
    'cache_key' => 'settings',
    'encrypt_value' => env('SETTING_ENCRYPT_VALUE', true),
    'cache' => env('SETTING_CACHE', true),
];
