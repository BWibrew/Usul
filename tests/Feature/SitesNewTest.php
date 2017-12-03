<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesNewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_new_site()
    {
        $this->mockResponses([
            ['headers' => ['Link' => $this->api_base_url.$this->api_root_uri]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->signIn()->post('/sites', [
            'url' => $this->api_base_url,
        ])->assertRedirect('/sites/1/');

        $this->assertDatabaseHas('sites', [
            'url' => $this->api_base_url,
            'root_uri' => $this->api_base_url.$this->api_root_uri,
            'name' => 'Example Site Name',
        ]);
    }

    /** @test */
    public function a_guest_cannot_store_a_new_site()
    {
        $this->withExceptionHandling();

        $this->mockResponses([
            ['headers' => ['Link' => $this->api_base_url.$this->api_root_uri]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->post('/sites', [
            'url' => $this->api_base_url,
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('sites', [
            'url' => $this->api_base_url,
            'root_uri' => $this->api_base_url.$this->api_root_uri,
            'name' => 'Example Site Name',
        ]);
    }

    /** @test */
    public function it_will_redirect_to_edit_page_if_discovery_fails()
    {
        $this->mockResponse([]);

        $this->signIn()->post('/sites', [
            'url' => $this->api_base_url,
        ])->assertRedirect('/sites/1/edit/')
          ->assertSessionHas('discovery', 'fail');

        $this->assertDatabaseHas('sites', [
            'url' => $this->api_base_url,
            'root_uri' => null,
            'name' => null,
        ]);
    }
}
