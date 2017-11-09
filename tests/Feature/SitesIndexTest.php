<?php

namespace Tests\Feature;

use App\Site;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $site;
    protected $user;
    protected $response;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
        $this->user = factory(User::class)->create();
        $this->response = $this->actingAs($this->user)->get('/sites');
    }

    /** @test */
    public function it_lists_site_id_and_name()
    {
        $this->response->assertSee((string) $this->site->id)
                       ->assertSee($this->site->name);
    }

    /** @test */
    public function it_lists_site_url()
    {
        $this->response->assertSee($this->site->url);
    }

    /** @test */
    public function it_links_to_remote_url()
    {
        $this->response->assertSee('href="'.$this->site->url.'"');
    }

    /** @test */
    public function it_links_to_detail_page()
    {
        $this->response->assertSee('href="http://usul.app/sites/'.$this->site->id.'"');
    }
}
