<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ApiConnections\Wordpress;

class WordpressTest extends TestCase
{
    protected $wp;

    public function setUp()
    {
        parent::setUp();

        $this->wp = new Wordpress;
    }

    /** @test */
    public function it_can_discover()
    {
        $response = $this->wp->discover('https://demo.wp-api.org/');

        $this->assertEquals('https://demo.wp-api.org/wp-json/', $response);
    }

    /** @test */
    public function it_can_discover_namespaces()
    {
        $response = $this->wp->namespaces('https://demo.wp-api.org/wp-json/');

        $this->assertContains('wp/v2', $response);
    }

    /** @test */
    public function it_can_retrieve_site_name()
    {
        $response = $this->wp->siteName('https://demo.wp-api.org/wp-json/');

        $this->assertEquals('WP REST API Demo', $response);
    }
}
