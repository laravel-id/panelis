<?php

namespace App\Models;

/**
 * The application's user model.
 *
 * Extend the package model so authentication and Panelis resources use the
 * same model while keeping the application's extension point local.
 */
class User extends \Panelis\User\Models\User {}
