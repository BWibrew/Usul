<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_displays_settings()
    {
        $this->logIn()->get('/sites/'.$this->site->id.'/auth')->assertViewIs('sites.auth');
    }
}
