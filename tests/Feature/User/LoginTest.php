<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login(): void
    {
        $response = $this->get('/login');

        $this->assertGuest();

        $response->assertOk()
            ->assertViewIs('pages.auth.login')
            ->assertSeeText(__('user.login'))
            ->assertSeeText(__('user.email'))
            ->assertSeeText(__('user.password'))
            ->assertSeeText(__('user.remember_login'))
            ->assertSeeText(__('user.forgot_password'))
            ->assertSeeText('user.btn_login');
    }

    public function test_user_already_logged_in(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/login');

        $response->assertStatus(302)
            ->assertRedirect('/');
    }

    /**
     * @throws \JsonException
     */
    public function test_login_existing_user(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();
    }

    public function test_login_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'invalid password',
            ]);

        $response->assertStatus(302)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email');
    }
}
