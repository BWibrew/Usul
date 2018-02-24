<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads_login_form()
    {
        $this->get('login')
             ->assertStatus(200)
             ->assertViewIs('auth.login');
    }

    /** @test */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create();

        $this->post('login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertRedirect('sites');

        $this->assertEquals($user->id, Auth::id());
    }

    /** @test */
    public function a_guest_cannot_log_in()
    {
        $this->expectException('Illuminate\Validation\ValidationException');

        $this->post('login', [
            'email' => 'email@example.com',
            'password' => 'secret',
        ]);
    }

    /** @test */
    public function it_can_log_out()
    {
        $this->signIn();

        $this->assertTrue(Auth::check());

        $this->post('logout');

        $this->assertEquals(null, Auth::user());
    }

    /** @test */
    public function it_loads_register_form()
    {
        $this->get('register')
             ->assertStatus(200)
             ->assertViewIs('auth.register');
    }

    /** @test */
    public function it_can_register_a_new_user()
    {
        $this->post('register', [
            'name' => 'name',
            'email' => 'email@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])->assertRedirect('sites');

        $this->assertTrue(Auth::check());
        $this->assertDatabaseHas('users', [
            'name' => 'name',
            'email' => 'email@example.com',
        ]);
    }

    /** @test */
    public function it_loads_password_reset_request_form()
    {
        $this->get('password/reset')
             ->assertStatus(200)
             ->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function it_sends_password_reset_email()
    {
        $user = factory(User::class)->create();

        Notification::fake();

        $this->post('password/email', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
