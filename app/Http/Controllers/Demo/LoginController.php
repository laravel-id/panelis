<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function guest(): RedirectResponse
    {
        $guest = User::query()
            ->firstOrCreate(['email' => 'guest@panelis.dev'], [
                'name' => 'Guest',
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(8)),
            ]);

        $branch = Branch::query()
            ->firstOrCreate(['slug' => 'default'], [
                'user_id' => $guest->id,
                'name' => 'Default',
            ]);

        $branch->users()->sync($guest->id);

        Auth::loginUsingId($guest->id);

        return redirect()->intended('/admin');
    }
}
