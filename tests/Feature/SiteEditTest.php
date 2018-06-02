<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SiteEditTest extends TestCase
{
    use RefreshDatabase;

    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();
    }

    /** @test */
    public function it_displays_name()
    {
        $this->logIn()->get('/sites/'.$this->site->id.'/edit')->assertSee($this->site->name);
    }

    /** @test */
    public function it_displays_url()
    {
        $this->logIn()->get('/sites/'.$this->site->id.'/edit')->assertSee($this->site->url);
    }

    /** @test */
    public function it_displays_root_uri()
    {
        $this->logIn()->get('/sites/'.$this->site->id.'/edit')->assertSee($this->site->root_uri);
    }

    /** @test */
    public function it_displays_warning_notice_if_discovery_failed()
    {
        $this->logIn()
            ->withSession(['discovery' => 'fail'])
            ->get('/sites/'.$this->site->id.'/edit')
            ->assertSee('Automatic discovery has failed. Please manually enter the information below.');
    }

    /** @test */
    public function a_guest_cannot_view_edit_page()
    {
        $this->withExceptionHandling();

        $this->get('/sites/'.$this->site->id.'/edit')->assertRedirect('/login');
    }

    /** @test */
    public function it_updates_name()
    {
        $site = factory(Site::class)->create(['name' => 'name']);
        $this->logIn()
             ->patch('/sites/'.$site->id, [
                 'name' => 'new name',
                 'url' => 'http://example.com',
                 'root_uri' => 'http://example.com/api',
             ])
             ->assertRedirect('/sites/'.$site->id.'/edit');

        $this->assertDatabaseHas('sites', [
            'name' => 'new name',
        ]);
    }

    /** @test */
    public function it_updates_url()
    {
        $site = factory(Site::class)->create(['url' => 'http://example.com']);
        $this->logIn()
             ->patch('/sites/'.$site->id, [
                 'name' => 'name',
                 'url' => 'http://example2.com',
                 'root_uri' => 'http://example.com/api',
             ])
             ->assertRedirect('/sites/'.$site->id.'/edit');

        $this->assertDatabaseHas('sites', [
            'url' => 'http://example2.com',
        ]);
    }

    /** @test */
    public function it_updates_root_uri()
    {
        $site = factory(Site::class)->create(['root_uri' => 'http://example.com/api']);
        $this->logIn()
             ->patch('/sites/'.$site->id, [
                 'name' => 'name',
                 'url' => 'http://example.com',
                 'root_uri' => 'http://example2.com/api',
             ])
             ->assertRedirect('/sites/'.$site->id.'/edit');

        $this->assertDatabaseHas('sites', [
            'root_uri' => 'http://example2.com/api',
        ]);
    }

    /** @test */
    public function a_guest_cannot_update()
    {
        $this->withExceptionHandling();

        $this->patch('/sites/'.$this->site->id)->assertRedirect('/login');
    }

    /** @test */
    public function it_soft_deletes()
    {
        $this->logIn()->delete('/sites/'.$this->site->id)->assertRedirect('/sites');

        $this->assertSoftDeleted('sites', ['id' => $this->site->id]);
    }

    /** @test */
    public function a_guest_cannot_delete()
    {
        $this->withExceptionHandling();

        $this->delete('/sites/'.$this->site->id)->assertRedirect('/login');

        $this->assertDatabaseHas('sites', ['id' => $this->site->id]);
    }

    /** @test */
    public function it_links_to_detail_page()
    {
        $this->logIn()
             ->get('/sites/'.$this->site->id.'/edit')
             ->assertSee('href="'.url('/sites/'.$this->site->id).'"');
    }
}
