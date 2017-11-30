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
    }

    /** @test */
    public function a_guest_cannot_view_index()
    {
        $this->get('/sites')->assertRedirect('/login');
    }

    /** @test */
    public function it_lists_site_id_and_name()
    {
        $this->login()
             ->assertSee((string) $this->site->id)
             ->assertSee($this->site->name);
    }

    /** @test */
    public function it_lists_site_url()
    {
        $this->login()->assertSee($this->site->url);
    }

    /** @test */
    public function it_links_to_remote_url()
    {
        $this->login()->assertSee('href="'.$this->site->url.'"');
    }

    /** @test */
    public function it_links_to_detail_page()
    {
        $this->login()->assertSee('href="http://usul.app/sites/'.$this->site->id.'"');
    }

    protected function login()
    {
        return $this->actingAs($this->user)->get('/sites');
    }
}
