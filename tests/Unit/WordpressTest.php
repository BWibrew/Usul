<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ApiConnections\Wordpress;

class WordpressTest extends TestCase
{
    protected $base_url = 'https://example.com/';
    protected $root_uri = 'api/';

    /** @test */
    public function it_can_discover()
    {
        $client = $this->mockClient(200, ['Link' => $this->base_url.$this->root_uri]);
        $response = (new Wordpress($client))->discover($this->base_url);

        $this->assertEquals($this->base_url.$this->root_uri, $response);
    }

    /** @test */
    public function it_can_discover_namespaces()
    {
        $client = $this->mockClient(200, [], ['namespaces' => ['wp/v2', ]]);
        $response = (new Wordpress($client))->namespaces($this->base_url.$this->root_uri);

        $this->assertContains('wp/v2', $response);
    }

    /** @test */
    public function it_can_retrieve_site_name()
    {
        $client = $this->mockClient(200, [], ['name' => 'Example Site Name']);
        $response = (new Wordpress($client))->siteName($this->base_url.$this->root_uri);

        $this->assertEquals('Example Site Name', $response);
    }
}
