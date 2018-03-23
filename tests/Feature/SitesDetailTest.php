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
        $this->mockResponses([[], []]);

        $this->logIn()->get('/sites/'.$this->site->id)->assertSee($this->site->name);
    }

    /** @test */
    public function it_displays_url()
    {
        $this->mockResponses([[], []]);

        $this->logIn()->get('/sites/'.$this->site->id)->assertSee($this->site->url);
    }

    /** @test */
    public function it_displays_root_uri()
    {
        $this->mockResponses([[], []]);

        $this->logIn()->get('/sites/'.$this->site->id)->assertSee($this->site->root_uri);
    }

    /** @test */
    public function it_links_to_edit_page()
    {
        $this->mockResponses([[], []]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertSee('href="'.url('/sites/'.$this->site->id.'/edit').'"');
    }

    /** @test */
    public function it_links_to_auth_settings_page()
    {
        $this->mockResponses([[], []]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertSee('href="'.url('/sites/'.$this->site->id.'/auth').'"');
    }

    /** @test */
    public function it_displays_a_notice_if_api_cannot_be_reached()
    {
        $this->mockResponses([['status_code' => 500], []]);

        $this->logIn()
             ->get('/sites/' . $this->site->id)
             ->assertViewHasAll([
                 'isConnected' => false,
                 'connection' => [
                     'wp_rest' => false,
                     'site_monitor' => true,
                     'authenticated' => true,
                 ],
             ])
             ->assertSee('API connection problem. Error code: 500');
    }

    /** @test */
    public function it_displays_wp_version()
    {
        $this->mockResponses([[], ['body' => '4.9.2']]);

        $this->logIn()->get('/sites/'.$this->site->id)->assertSee('4.9.2');
    }

    /** @test */
    public function it_displays_a_notice_when_api_is_not_authenticated()
    {
        $this->mockResponses([[], ['status_code' => 401]]);

        $this->logIn()
             ->get('/sites/' . $this->site->id)
             ->assertViewHasAll([
                 'isConnected' => true,
                 'connection'  => [
                     'wp_rest'       => true,
                     'site_monitor'  => true,
                     'authenticated' => false,
                 ],
             ])
             ->assertSee('API connection problem. Error code: 401');
    }
}
