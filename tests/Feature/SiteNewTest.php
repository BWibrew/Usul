<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteNewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_new_site()
    {
        $this->mockResponses([
            ['headers' => ['Link' => self::API_BASE_URL.'/'.self::API_ROOT_URI]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->logIn()->post('/sites', [
            'url' => self::API_BASE_URL,
        ])->assertRedirect('/sites/1/');

        $this->assertDatabaseHas('sites', [
            'url' => self::API_BASE_URL,
            'root_uri' => self::API_BASE_URL.'/'.self::API_ROOT_URI,
            'name' => 'Example Site Name',
        ]);
    }

    /** @test */
    public function a_guest_cannot_store_a_new_site()
    {
        $this->withExceptionHandling();

        $this->mockResponses([
            ['headers' => ['Link' => self::API_BASE_URL.'/'.self::API_ROOT_URI]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->post('/sites', [
            'url' => self::API_BASE_URL,
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('sites', [
            'url' => self::API_BASE_URL,
            'root_uri' => self::API_BASE_URL.'/'.self::API_ROOT_URI,
            'name' => 'Example Site Name',
        ]);
    }

    /** @test */
    public function it_will_redirect_to_edit_page_if_discovery_fails()
    {
        $this->mockResponse([]);

        $this->logIn()->post('/sites', [
            'url' => self::API_BASE_URL,
        ])->assertRedirect('/sites/1/edit/')
             ->assertSessionHas('discovery', 'fail');

        $this->assertDatabaseHas('sites', [
            'url' => self::API_BASE_URL,
            'root_uri' => null,
            'name' => null,
        ]);
    }

    /** @test */
    public function it_can_be_added_then_deleted_then_added_again()
    {
        $this->mockResponses([
            ['headers' => ['Link' => self::API_BASE_URL.'/'.self::API_ROOT_URI]],
            ['body' => ['name' => 'Example Site Name']],
            ['headers' => ['Link' => self::API_BASE_URL.'/'.self::API_ROOT_URI]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->logIn()->post('/sites', ['url' => self::API_BASE_URL]);

        $this->assertDatabaseHas('sites', [
            'id' => 1,
            'url' => self::API_BASE_URL,
            'root_uri' => self::API_BASE_URL.'/'.self::API_ROOT_URI,
            'name' => 'Example Site Name',
            'deleted_at' => null,
        ]);

        $this->logIn()->delete('/sites/1');

        $this->logIn()->post('/sites', ['url' => self::API_BASE_URL]);

        $this->assertDatabaseHas('sites', [
            'id' => 2,
            'url' => self::API_BASE_URL,
            'root_uri' => self::API_BASE_URL.'/'.self::API_ROOT_URI,
            'name' => 'Example Site Name',
            'deleted_at' => null,
        ]);
    }
}
