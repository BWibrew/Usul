<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function a_guest_cannot_view_detail_page()
    {
        $this->withExceptionHandling();

        $this->get('/sites/'.$this->site->id)->assertRedirect('/login');
    }

    /** @test */
    public function it_displays_name()
    {
        $this->signIn()->get('/sites/'.$this->site->id)->assertSee($this->site->name);
    }

    /** @test */
    public function it_displays_url()
    {
        $this->signIn()->get('/sites/'.$this->site->id)->assertSee($this->site->url);
    }

    /** @test */
    public function it_displays_root_uri()
    {
        $this->signIn()->get('/sites/'.$this->site->id)->assertSee($this->site->root_uri);
    }

    /** @test */
    public function it_links_to_edit_page()
    {
        $this->signIn()
             ->get('/sites/'.$this->site->id)
             ->assertSee('href="'.url('/sites/'.$this->site->id.'/edit').'"');
    }

    /** @test */
    public function it_displays_a_notice_if_api_cannot_be_reached()
    {
        $this->mockResponse(['status_code' => 500]);

        $this->signIn()->get('/sites/'.$this->site->id)->assertSee('Could not connect to API');
    }
}
