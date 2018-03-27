<?php

namespace Tests\Unit;

use Tests\TestCase;

class WordpressTest extends TestCase
{
    /** @test */
    public function it_can_discover()
    {
        $this->mockResponse(['headers' => ['Link' => self::API_BASE_URL.self::API_ROOT_URI]]);
        $response = $this->wordpress()->discover(self::API_BASE_URL);

        $this->assertEquals(self::API_BASE_URL.self::API_ROOT_URI, $response);
    }

    /** @test */
    public function it_throws_an_exception_when_a_url_does_not_return_link_header()
    {
        $this->expectException('Exception');
        $this->mockResponse();
        $this->wordpress()->discover(self::API_BASE_URL);
    }

    /** @test */
    public function it_throws_an_exception_when_a_url_cannot_be_reached()
    {
        $this->expectException('Exception');
        $this->mockResponse(['status_code' => 500]);
        $this->wordpress()->discover(self::API_BASE_URL);
    }

    /** @test */
    public function it_can_discover_namespaces()
    {
        $this->mockResponse(['body' => ['namespaces' => ['wp/v2', 'wp-site-monitor/v1']]]);
        $response = $this->wordpress()->namespaces(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertEquals(['wp/v2', 'wp-site-monitor/v1'], $response);
    }

    /** @test */
    public function it_can_retrieve_site_name()
    {
        $this->mockResponse(['body' => ['name' => 'Example Site Name']]);
        $response = $this->wordpress()->siteName(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertEquals('Example Site Name', $response);
    }

    /** @test */
    public function it_can_retrieve_version()
    {
        $this->mockResponse(['body' => '4.9.2']);

        $response = $this->wordpress()->version(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertEquals('4.9.2', $response);
    }

    /** @test */
    public function it_authenticates_using_json_web_tokens()
    {
        $expectedResponse = [
            'token'             => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9qd3QuZGV2IiwiaWF0IjoxND
                M4NTcxMDUwLCJuYmYiOjE0Mzg1NzEwNTAsImV4cCI6MTQzOTE3NTg1MCwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.YNe6AyWW4
                B7ZwfFE5wJ0O6qQ8QFcYizimDmBy6hCH_8',
            'user_display_name' => 'admin',
            'user_email'        => 'admin@localhost.dev',
            'user_nicename'     => 'admin',
        ];

        $this->mockResponse(['body' => $expectedResponse]);

        $response = $this->wordpress()->jwtAuth(self::API_BASE_URL.self::API_ROOT_URI, [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $this->assertEquals($expectedResponse, $response);
    }

    /** @test */
    public function it_throws_an_exception_when_json_web_token_authentication_fails()
    {
        $this->expectException('Exception');
        $this->mockResponse(['status_code' => 403]);
        $this->wordpress()->jwtAuth(self::API_BASE_URL.self::API_ROOT_URI, [
            'username' => 'admin',
            'password' => 'password',
        ]);
    }

    /** @test */
    public function it_uses_jwt_auth_token_in_requests()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9qd3QuZGV2IiwiaWF0IjoxNDM4NTcxMDUwLCJuYmY'
            .'iOjE0Mzg1NzEwNTAsImV4cCI6MTQzOTE3NTg1MCwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.YNe6AyWW4B7ZwfFE5wJ0O6qQ8QF'
            .'cYizimDmBy6hCH_8';
        $requests = &$this->mockResponses([
            ['headers' => ['Link' => '']],
            ['body' => ['namespaces' => '']],
            ['body' => ['name' => '']],
            [],
            [],
        ]);

        $wpConnection = $this->wordpress();
        $wpConnection->authType('jwt');
        $wpConnection->authToken($token);

        $wpConnection->version(self::API_BASE_URL.self::API_ROOT_URI);
        $wpConnection->plugins(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertCount(2, $requests);

        foreach ($requests as $request) {
            $this->assertEquals('Bearer '.$token, $request['request']->getHeader('Authorization')[0]);
        }
    }

    /** @test */
    public function it_can_retrieve_plugins_list()
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
        $this->mockResponse(['body' => $expectedPlugins]);

        $response = $this->wordpress()->plugins(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertEquals($expectedPlugins, $response);
    }
}
