<?php

namespace Tests\Unit;

use App\ApiConnections\Wordpress;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
