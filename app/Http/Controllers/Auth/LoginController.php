<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function form(): View
    {
        return view('pages.auth.login')
            ->with('title', __('user.login'));
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $remember = (bool) $request->input('remember');
        if (Auth::attempt($request->only(['email', 'password']), $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['email' => __('user.invalid_credentials')])
            ->onlyInput('email');
    }
}
