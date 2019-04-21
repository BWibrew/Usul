<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    public function setUp(): void
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function a_guest_cannot_view_index()
    {
        $this->withExceptionHandling();

        $this->get('/sites')->assertRedirect('/login');
    }

    /** @test */
    public function it_lists_site_id_and_name()
    {
        $this->logIn()->get('/sites')->assertSee((string) $this->site->id)->assertSee($this->site->name);
    }

    /** @test */
    public function it_lists_site_url()
    {
        $this->logIn()->get('/sites')->assertSee($this->site->url);
    }

    /** @test */
    public function it_links_to_remote_url()
    {
        $this->logIn()->get('/sites')->assertSee('href="'.$this->site->url.'"');
    }

    /** @test */
    public function it_links_to_detail_page()
    {
        $this->logIn()->get('/sites')->assertSee('href="'.url('/sites/'.$this->site->id).'"');
    }
}
