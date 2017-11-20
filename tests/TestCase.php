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

    protected $api_base_url = 'https://example.com/';
    protected $api_root_uri = 'api/';

    /**
     * Creates queue of mock responses for the Guzzle client.
     *
     * @param array $responses
     *
     * @return Client
     */
    protected function mockResponses(array $responses = [])
    {
        $queue = [];
        foreach ($responses as $response) {
            $status_code = isset($response['status_code']) ? $response['status_code'] : 200;
            $headers = isset($response['headers']) ? $response['headers'] : [];
            $body = isset($response['body']) ? $response['body'] : [];

            array_push($queue, new Response(
                $status_code,
                $headers + ['Content-Type' => 'application/json'],
                GuzzleHttp\json_encode($body)
            ));
        }

        $mock = new MockHandler($queue);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $this->app->instance('GuzzleHttp\Client', $client);
    }

    /**
     * Wrapper for mockResponses for use with a single response.
     *
     * @param array $response
     */
    protected function mockResponse(array $response = [])
    {
        $this->mockResponses([$response]);
    }

    /**
     * Resolve an instance of Wordpress from the service container.
     *
     * @return \App\ApiConnections\Wordpress;
     */
    protected function wordpress()
    {
        return $this->app->make('ApiConnections\Wordpress');
    }
}
