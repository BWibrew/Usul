<?php

namespace Tests;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Client;
use App\Exceptions\Handler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $api_base_url = 'https://example.com/';
    protected $api_root_uri = 'api/';
    protected $oldExceptionHandler;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();
    }

    protected function signIn($user = null)
    {
        $user = $user ?: factory('App\User')->create();

        $this->actingAs($user);

        return $this;
    }

    // hat tip: https://github.com/laracasts/Lets-Build-a-Forum-in-Laravel/blob/fde014c59935b174b913d8c91ab03d2c114dc886/tests/TestCase.php
    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct()
            {
            }

            public function report(Exception $e)
            {
            }

            public function render($request, Exception $e)
            {
                throw $e;
            }
        });
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }

    /**
     * Creates queue of mock responses for the Guzzle client.
     *
     * @param array $responses
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
