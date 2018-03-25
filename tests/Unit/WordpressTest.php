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
        $this->mockResponse(['body' => ['namespaces' => ['wp/v2']]]);
        $response = $this->wordpress()->namespaces(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertContains('wp/v2', $response);
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

        $response = $this->wordpress()->version(self::API_BASE_URL.self::API_ROOT_URI.'/wp-site-monitor/v1/wp-version');

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

        $wpConnection->discover(self::API_BASE_URL.self::API_ROOT_URI);
        $wpConnection->namespaces(self::API_BASE_URL.self::API_ROOT_URI);
        $wpConnection->siteName(self::API_BASE_URL.self::API_ROOT_URI);
        $wpConnection->apiConnected(self::API_BASE_URL.self::API_ROOT_URI);
        $wpConnection->version(self::API_BASE_URL.self::API_ROOT_URI);

        $this->assertCount(5, $requests);

        foreach ($requests as $request) {
            $this->assertEquals('Bearer '.$token, $request['request']->getHeader('Authorization')[0]);
        }
    }
}
