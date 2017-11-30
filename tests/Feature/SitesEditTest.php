<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_view_edit_page()
    {
        $site = factory(Site::class)->create();
        $this->get('/sites/'.$site->id.'/edit')->assertRedirect('/login');
    }

    /** @test */
    public function a_guest_cannot_update()
    {
        $site = factory(Site::class)->create();
        $this->patch('/sites/'.$site->id)->assertRedirect('/login');
    }
}
