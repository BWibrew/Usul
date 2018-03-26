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
        $this->mockResponses([['status_code' => 500], [], ['status_code' => 500], []]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertViewHasAll([
                 'isConnected' => false,
                 'connection' => [
                     'wp_rest' => false,
                     'site_monitor' => false,
                     'authenticated' => false,
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
        $this->mockResponses([
            [],
            ['status_code' => 401],
            ['body' => ['namespaces' => ['wp/v2', 'wp-site-monitor/v1']]],
            []
        ]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
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

    /** @test */
    public function it_displays_a_notice_if_wp_site_monitor_namespace_is_not_detected()
    {
        $this->mockResponses([[], [], ['body' => ['namespaces' => ['wp/v2', 'not-wp-site-monitor/v1']]], []]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertViewHasAll([
                 'isConnected' => true,
                 'connection'  => [
                     'wp_rest'       => true,
                     'site_monitor'  => false,
                     'authenticated' => true,
                 ],
                 'namespaces' => ['wp/v2', 'not-wp-site-monitor/v1'],
             ])
             ->assertSee('WP Site Monitor not detected.');
    }

    /** @test */
    public function it_displays_plugins_list()
    {
        $expectedPlugins = [
            'akismet/akismet.php' => [
                'Name' => 'Akismet Anti-Spam',
                'PluginURI' => 'https://akismet.com/',
                'Version' => '4.0.3',
                'Description' => 'Used by millions, Akismet is quite possibly the best way in the world to <strong>'
                                 .'protect your blog from spam</strong>. It keeps your site protected even while you '
                                 .'sleep. To get started => activate the Akismet plugin and then go to your Akismet '
                                 .'Settings page to set up your API key.',
                'Author' => 'Automattic',
                'AuthorURI' => 'https://automattic.com/wordpress-plugins/',
                'TextDomain' => 'akismet',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'Akismet Anti-Spam',
                'AuthorName' => 'Automattic',
                'Active' => true,
            ],
            'hello-dolly/hello.php' => [
                'Name' => 'Hello Dolly',
                'PluginURI' => 'https://wordpress.org/plugins/hello-dolly/',
                'Version' => '1.6',
                'Description' => 'This is not just a plugin, it symbolizes the hope and enthusiasm of an entire '
                                 .'generation summed up in two words sung most famously by Louis Armstrong => Hello, '
                                 .'Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> '
                                 .'in the upper right of your admin screen on every page.',
                'Author' => 'Matt Mullenweg',
                'AuthorURI' => 'https://ma.tt/',
                'TextDomain' => 'hello-dolly',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'Hello Dolly',
                'AuthorName' => 'Matt Mullenweg',
                'Active' => false,
            ],
        ];
        $this->mockResponses([[], [], [], ['body' => $expectedPlugins]]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertViewHas('plugins', $expectedPlugins)
             ->assertSee('Akismet Anti-Spam')
             ->assertSee('Hello Dolly');
    }

    /** @test */
    public function it_displays_namespaces()
    {
        $this->mockResponses([[], [], ['body' => ['namespaces' => ['wp/v2', 'wp-site-monitor/v1']]], []]);

        $this->logIn()
             ->get('/sites/'.$this->site->id)
             ->assertSee('wp/v2')
             ->assertSee('wp-site-monitor/v1');
    }
}
