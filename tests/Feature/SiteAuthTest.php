<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    public function setUp(): void
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function a_guest_cannot_view_auth_settings()
    {
        $this->withExceptionHandling();

        $this->get('/sites/'.$this->site->id.'/auth')->assertRedirect('/login');
    }

    /** @test */
    public function it_displays_auth_type()
    {
        $this->logIn()->get('/sites/'.$this->site->id.'/auth')->assertSee($this->site->auth_type);
    }

    /** @test */
    public function it_displays_message_when_not_authenticated_instead_of_auth_type()
    {
        $site = factory(Site::class)->create(['auth_type' => null]);

        $this->logIn()->get('/sites/'.$site->id.'/auth')->assertSee('Not Authenticated');
    }

    /** @test */
    public function it_updates_auth_token_with_jwt()
    {
        $site = factory(Site::class)->create(['auth_token' => null]);
        $this->mockResponse(['body' => [
            'token'             => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9qd3QuZGV2IiwiaWF0IjoxND'
                .'M4NTcxMDUwLCJuYmYiOjE0Mzg1NzEwNTAsImV4cCI6MTQzOTE3NTg1MCwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.YNe6AyW'
                .'W4B7ZwfFE5wJ0O6qQ8QFcYizimDmBy6hCH_8',
            'user_display_name' => 'admin',
            'user_email'        => 'admin@localhost.dev',
            'user_nicename'     => 'admin',
        ]]);

        $this->logIn()
             ->patch('/sites/'.$site->id.'/auth', [
                 'type' => 'jwt',
                 'username' => 'admin',
                 'password' => 'password',
             ])
             ->assertRedirect('/sites/'.$site->id.'/auth');

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'auth_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9qd3QuZGV2IiwiaWF0IjoxNDM4NTcxM'
                .'DUwLCJuYmYiOjE0Mzg1NzEwNTAsImV4cCI6MTQzOTE3NTg1MCwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.YNe6AyWW4B7Zwf'
                .'FE5wJ0O6qQ8QFcYizimDmBy6hCH_8',
        ]);
    }

    /** @test */
    public function it_updates_auth_type_with_jwt()
    {
        $site = factory(Site::class)->create(['auth_type' => null]);
        $this->mockResponse(['body' => ['token' => '']]);

        $this->logIn()
             ->patch('/sites/'.$site->id.'/auth', [
                 'type' => 'jwt',
                 'username' => 'admin',
                 'password' => 'password',
             ])
             ->assertRedirect('/sites/'.$site->id.'/auth');

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'auth_type' => 'jwt',
        ]);
    }

    /** @test */
    public function it_displays_error_message_when_api_connection_fails()
    {
        $this->mockResponse(['status_code' => 500]);

        $this->logIn()
             ->patch('/sites/'.$this->site->id.'/auth', [
                 'type' => 'jwt',
                 'username' => 'admin',
                 'password' => 'password',
             ])
             ->assertRedirect('/sites/'.$this->site->id.'/auth')
             ->assertSessionHas('status', 'API connection problem. Error code: 500');
    }

    /** @test */
    public function it_displays_error_message_when_authentication_fails()
    {
        $this->mockResponse(['status_code' => 403]);

        $this->logIn()
             ->patch('/sites/'.$this->site->id.'/auth', [
                 'type' => 'jwt',
                 'username' => 'admin',
                 'password' => 'wrong_password',
             ])
             ->assertRedirect('/sites/'.$this->site->id.'/auth')
             ->assertSessionHas('status', 'API connection problem. Error code: 403');
    }

    /** @test */
    public function a_guest_cannot_update()
    {
        $this->withExceptionHandling();

        $this->patch('/sites/'.$this->site->id.'/auth')->assertRedirect('/login');
    }
}
