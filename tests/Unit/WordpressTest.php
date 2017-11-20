<?php

namespace Tests\Unit;

use Tests\TestCase;

class WordpressTest extends TestCase
{
    /** @test */
    public function it_can_discover()
    {
        $this->mockResponse(['headers' => ['Link' => $this->api_base_url.$this->api_root_uri]]);
        $response = $this->wordpress()->discover($this->api_base_url);

        $this->assertEquals($this->api_base_url.$this->api_root_uri, $response);
    }

    /** @test */
    public function it_throws_an_exception_when_a_url_does_not_return_link_header()
    {
        $this->expectException('Exception');
        $this->mockResponse();
        $response = $this->wordpress()->discover($this->api_base_url);

        $this->assertEquals($this->api_base_url.$this->api_root_uri, $response);
    }

    /** @test */
    public function it_throws_an_exception_when_a_url_cannot_be_reached()
    {
        $this->expectException('Exception');
        $this->mockResponse(['status_code' => 500]);
        $response = $this->wordpress()->discover($this->api_base_url);

        $this->assertEquals($this->api_base_url.$this->api_root_uri, $response);
    }

    /** @test */
    public function it_can_discover_namespaces()
    {
        $this->mockResponse(['body' => ['namespaces' => ['wp/v2']]]);
        $response = $this->wordpress()->namespaces($this->api_base_url.$this->api_root_uri);

        $this->assertContains('wp/v2', $response);
    }

    /** @test */
    public function it_can_retrieve_site_name()
    {
        $this->mockResponse(['body' => ['name' => 'Example Site Name']]);
        $response = $this->wordpress()->siteName($this->api_base_url.$this->api_root_uri);

        $this->assertEquals('Example Site Name', $response);
    }
}
