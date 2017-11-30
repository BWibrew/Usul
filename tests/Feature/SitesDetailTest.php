<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_view_detail_page()
    {
        $site = factory(Site::class)->create();
        $this->get('/sites/'.$site->id)->assertRedirect('/login');
    }
}
