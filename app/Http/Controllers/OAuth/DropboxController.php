<?php

namespace App\Http\Controllers\OAuth;

use App\Events\SettingUpdated;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\OAuth\OAuthFactory;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class DropboxController extends Controller
{
    public function __invoke(Request $request, OAuthFactory $oauth): RedirectResponse
    {
        $request->validate([
            'code' => ['required'],
            'state' => ['required'],
        ]);

        try {
            $states = json_decode(Crypt::decryptString($request->input('state')), true);

            $auth = $oauth->setAppKey(config('dropbox.client_id'))
                ->setAppSecret(config('dropbox.client_secret'))
                ->setRedirectUri(route('callback.dropbox'))
                ->setAuthorizationCode($request->input('code'))
                ->authorize();

            if (! empty($auth->getError())) {
                Log::error($auth->getError());
            }

            if (! empty($auth->getRefreshToken())) {
                Setting::set('filesystems.disks.dropbox.token', $auth->getToken());
                Setting::set('dropbox.refresh_token', $auth->getRefreshToken());
                Setting::set('dropbox.expired_at', $auth->getExpiresIn());

                event(new SettingUpdated);
            }

            return redirect(data_get($states, 'redirect', '/'));
        } catch (DecryptException $e) {
            Log::warning($e->getMessage());
        }

        return redirect('/');
    }
}
