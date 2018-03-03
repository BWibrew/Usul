<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_login_form()
    {
        $this->get('login')
             ->assertStatus(200)
             ->assertViewIs('auth.login');
    }

    /** @test */
    public function it_redirects_user_to_sites_after_login()
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
    public function it_shows_register_form()
    {
        $this->get('register')
             ->assertStatus(200)
             ->assertViewIs('auth.register');
    }

    /** @test */
    public function it_redirects_user_to_sites_after_registering()
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
    public function it_shows_password_reset_request_form()
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

    /** @test */
    public function it_saves_password_reset_in_database()
    {
        $user = factory(User::class)->create();

        Notification::fake();

        $this->post('password/email', [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_shows_password_reset_form()
    {
        $this->get('password/reset/dummy_token')
             ->assertStatus(200)
             ->assertViewIs('auth.passwords.reset');
    }

    /** @test */
    public function it_redirects_to_sites_after_resetting_password()
    {
        $user = factory(User::class)->create();

        $key = 'kS37T0FKPW74HVEtAedXR1Mub2/OTlHQ503Vn5Aaja8=';
        $token = hash_hmac('sha256', Str::random(40), $key);
        $hashedValue = app(BcryptHasher::class)->make($token);

        DB::table('password_resets')->insert([
                'email'      => $user->email,
                'token'      => $hashedValue,
        ]);

        $this->post('password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('sites');
    }
}
