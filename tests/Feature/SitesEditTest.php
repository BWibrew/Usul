<?php

namespace Tests\Feature;

use App\Site;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesEditTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function it_displays_name()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id.'/edit')->assertSee($site->name);
    }

    /** @test */
    public function it_displays_url()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id.'/edit')->assertSee($site->url);
    }

    /** @test */
    public function it_displays_root_uri()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)->get('/sites/'.$site->id.'/edit')->assertSee($site->root_uri);
    }

    /** @test */
    public function it_displays_warning_notice_if_discovery_failed()
    {
        $site = factory(Site::class)->create();
        $this->actingAs($this->user)
            ->withSession(['discovery' => 'fail'])
            ->get('/sites/'.$site->id.'/edit')
            ->assertSee('Automatic discovery has failed. Please manually enter the information below.');
    }

    /** @test */
    public function a_guest_cannot_view_edit_page()
    {
        $site = factory(Site::class)->create();
        $this->get('/sites/'.$site->id.'/edit')->assertRedirect('/login');
    }

    /** @test */
    public function it_updates_name()
    {
        $site = factory(Site::class)->create(['name' => 'name']);
        $this->actingAs($this->user)
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
        $this->actingAs($this->user)
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
        $this->actingAs($this->user)
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
        $site = factory(Site::class)->create();
        $this->patch('/sites/'.$site->id)->assertRedirect('/login');
    }

    /** @test */
    public function it_soft_deletes()
    {
        $site = factory(Site::class)->create();

        $this->actingAs($this->user)->delete('/sites/'.$site->id)->assertRedirect('/sites');

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
    }

    /** @test */
    public function a_guest_cannot_delete()
    {
        $site = factory(Site::class)->create();

        $this->delete('/sites/'.$site->id)->assertRedirect('/login');

        $this->assertDatabaseHas('sites', ['id' => $site->id]);
    }
}
