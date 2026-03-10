<?php

namespace Modules\User\Actions;

use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Models\User;

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
