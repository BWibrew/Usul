<?php

namespace Tests\Unit;

use Tests\TestCase;

class WordpressTest extends TestCase
{
    /** @test */
    public function it_can_discover()
    {
        $this->mockResponse(['headers' => ['Link' => $this->api_base_url.$this->api_root_uri]]);
        $response = resolve('ApiConnections\Wordpress')->discover($this->api_base_url);

        $this->assertEquals($this->api_base_url.$this->api_root_uri, $response);
    }

    /** @test */
    public function it_can_discover_namespaces()
    {
        $this->mockResponse(['body' => ['namespaces' => ['wp/v2']]]);
        $response = resolve('ApiConnections\Wordpress')->namespaces($this->api_base_url.$this->api_root_uri);

        $this->assertContains('wp/v2', $response);
    }

    /** @test */
    public function it_can_retrieve_site_name()
    {
        $this->mockResponse(['body' => ['name' => 'Example Site Name']]);
        $response = resolve('ApiConnections\Wordpress')->siteName($this->api_base_url.$this->api_root_uri);

        $this->assertEquals('Example Site Name', $response);
    }
}
