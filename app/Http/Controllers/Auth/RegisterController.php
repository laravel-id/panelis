<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    private function checkDefaultRole(): void
    {
        abort_if(
            empty(config('user.default_role')),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            __('Default role is not set.'),
        );
    }

    public function form(): View
    {
        $this->checkDefaultRole();

        return view('pages.auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->checkDefaultRole();

        DB::transaction(function () use ($request): RedirectResponse {
            $request->merge(['password' => Hash::make($request->input('password'))]);

            $role = Role::findById(config('user.default_role'));

            $user = User::query()->create($request->validated());
            $user->assignRole($role);

            Auth::login($user, true);

            event(new Registered($user));

            return to_route('index');
        });

        return redirect()->back();
    }
}
