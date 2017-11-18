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
    protected $base_url = 'https://example.com/';
    protected $root_uri = 'api/';

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_can_discover_root_uri_from_api()
    {
        $client = $this->mockClient(200, ['Link' => $this->base_url.$this->root_uri]);
        $this->site->root_uri = (new Wordpress($client))->discover($this->base_url);
        $this->site->save();

        $this->assertDatabaseHas('sites', ['root_uri' => $this->base_url.$this->root_uri]);
    }

    /** @test */
    public function it_can_populate_name_from_api()
    {
        $client = $this->mockClient(200, [], ['name' => 'Example Site Name']);
        $this->site->name = (new Wordpress($client))->siteName($this->base_url.$this->root_uri);
        $this->site->save();

        $this->assertDatabaseHas('sites', ['name' => 'Example Site Name']);
    }

    /** @test */
    public function it_can_populate_namespaces_from_api()
    {
        $client = $this->mockClient(200, [], ['namespaces' => ['wp/v2', ]]);
        $this->site->namespaces = json_encode((new Wordpress($client))->namespaces($this->base_url.$this->root_uri));
        $this->site->save();

        $this->assertDatabaseHas('sites', ['namespaces' => json_encode(['wp/v2'])]);
    }
}
