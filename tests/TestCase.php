<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,
        MocksGuzzleResponses;

    const API_BASE_URL = 'https://example.com';
    const API_ROOT_URI = 'api/v1';

    protected function setUp()
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    /**
     * Create a new user and login to the application.
     *
     * @param null $user
     *
     * @return $this
     */
    protected function logIn($user = null)
    {
        $user = $user ?: factory('App\User')->create();

        $this->actingAs($user);

        return $this;
    }

    /**
     * Resolve an instance of Wordpress from the service container.
     *
     * @return \App\ApiConnections\Wordpress;
     */
    protected function wordpress()
    {
        return $this->app->make(\App\ApiConnections\Wordpress::class);
    }
}
