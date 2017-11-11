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

        $this->actingAs($this->user)->post('/sites', [
            'url' => 'https://demo.wp-api.org/'
        ])->assertRedirect('/sites');

        $this->assertDatabaseHas('sites', [
            'url' => 'https://demo.wp-api.org/',
            'root_uri' => 'https://demo.wp-api.org/wp-json/',
            'name' => 'WP REST API Demo',
        ]);
    }
}
