<?php

namespace App\Actions\User;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Lorisleiva\Actions\Concerns\AsAction;

class SendResetPasswordLink
{
    use AsAction;

    public function handle(User $user): void
    {
        $token = app('auth.password.broker')->createToken($user);
        $notification = new ResetPassword($token);
        $notification->url = Filament::getResetPasswordUrl($token, $user);

        $user->notify($notification);
    }
}
