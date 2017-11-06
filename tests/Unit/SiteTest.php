<?php

namespace Tests\Unit;

use App\Site;
use Tests\TestCase;
use App\ApiConnections\Wordpress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected $site;
    protected $wp;

    public function setUp() {
        parent::setUp();

        $this->wp = new Wordpress;
        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_can_discover_root_uri_from_api()
    {
        $this->site->root_uri = $this->wp->discover('https://demo.wp-api.org/');
        $this->site->save();

        $this->assertDatabaseHas('sites', ['root_uri' => $this->site->root_uri]);
    }

    /** @test */
    public function it_can_populate_name_from_api()
    {
        $this->site->name = $this->wp->siteName('https://demo.wp-api.org/wp-json/');
        $this->site->save();

        $this->assertDatabaseHas('sites', ['name' => $this->site->name]);
    }

    /** @test */
    public function it_can_populate_namespaces_from_api()
    {
        $this->site->namespaces = $this->wp->siteName('https://demo.wp-api.org/wp-json/');
        $this->site->save();

        $this->assertDatabaseHas('sites', ['namespaces' => $this->site->namespaces]);
    }
}
