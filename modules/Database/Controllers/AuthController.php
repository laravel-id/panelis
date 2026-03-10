<?php

namespace Modules\Database\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Modules\Database\Panel\Clusters\Databases\Pages\AutoBackup;
use Modules\Setting\Models\Setting;

class AuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return Socialite::driver(config('database.cloud_storage'))
            ->scopes($request->get('scopes', []))
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $user = Socialite::driver(config('database.cloud_storage'))->user();

        Setting::set('filesystems.disks.dropbox.token', $user->token);
        Setting::set('services.dropbox.refresh_token', $user->refreshToken);
        Setting::set('services.dropbox.expires_in', $user->expiresIn);

        return redirect(AutoBackup::getUrl());
    }
}
