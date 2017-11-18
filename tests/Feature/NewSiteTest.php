<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewSiteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function it_stores_new_site()
    {
        $this->withoutMiddleware();
        $this->mockResponses([
            ['headers' => ['Link' => $this->api_base_url.$this->api_root_uri]],
            ['body' => ['name' => 'Example Site Name']],
        ]);

        $this->actingAs($this->user)->post('/sites', [
            'url' => $this->api_base_url
        ])->assertRedirect('/sites');

        $this->assertDatabaseHas('sites', [
            'url' => $this->api_base_url,
            'root_uri' => $this->api_base_url.$this->api_root_uri,
            'name' => 'Example Site Name',
        ]);
    }
}
