<?php

namespace Tests;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Creates a new Guzzle client with a mock response.
     *
     * @param int $status_code
     * @param array $headers
     * @param array $body
     *
     * @return Client
     */
    protected function mockClient($status_code = 200, $headers = [], $body = [])
    {
        $mock = new MockHandler([
            new Response(
                $status_code,
                $headers + ['Content-Type' => 'application/json'],
                GuzzleHttp\json_encode($body)
            ),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
