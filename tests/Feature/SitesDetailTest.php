<?php

namespace Tests\Feature;

use App\Site;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_guest_cannot_view_detail_page()
    {
        $site = factory(Site::class)->create();
        $this->get('/sites/'.$site->id)->assertRedirect('/login');
    }

    /** @test */
    public function it_displays_name()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id)->assertSee($site->name);
    }

    /** @test */
    public function it_displays_url()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id)->assertSee($site->url);
    }

    /** @test */
    public function it_displays_root_uri()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id)->assertSee($site->root_uri);
    }

    /** @test */
    public function it_links_to_edit_page()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id)->assertSee('/sites/'.$site->id.'/edit');
    }

    /** @test */
    public function it_displays_a_notice_if_api_cannot_be_reached()
    {
        $site = factory(Site::class)->create();
        $this->mockResponse(['status_code' => 500]);

        $this->actingAs($this->user)
             ->get('/sites/'.$site->id)
             ->assertSee('Could not connect to API');
    }
}
