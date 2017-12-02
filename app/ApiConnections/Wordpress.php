<?php

namespace App\ApiConnections;

use Exception;
use GuzzleHttp\Client;

class Wordpress
{
    protected $api;

    public function __construct(Client $client)
    {
        $this->api = $client;
    }

    /**
     * Check if api is enabled and return root URI.
     *
     * @param string $uri
     *
     * @return mixed
     * @throws Exception
     */
    public function discover(string $uri)
    {
        $response = $this->apiGet($uri);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Could not connect to URL.');
        }

        $response = $response->getHeader('Link');

        if (! count($response) > 0) {
            throw new Exception('API root could not be discovered');
        }

        return str_replace(['<', '>'], '', explode(';', $response[0])[0]);
    }

    /**
     * Get the API namespaces.
     *
     * @param string $uri
     *
     * @return mixed
     */
    public function namespaces(string $uri)
    {
        $response = (string) $this->apiGet($uri)->getBody();

        return json_decode($response, true)['namespaces'];
    }

    /**
     * Get the site name.
     *
     * @param string $uri
     *
     * @return mixed
     */
    public function siteName(string $uri)
    {
        $response = (string) $this->apiGet($uri)->getBody();

        return json_decode($response, true)['name'];
    }

    /**
     * Check for successful connection to remote API.
     *
     * @param string $uri
     *
     * @return bool
     */
    public function apiConnected(string $uri)
    {
        $response = $this->apiGet($uri);

        if ($response->getStatusCode() !== 200 || is_null(json_decode($response->getBody()))) {
            return false;
        }

        return true;
    }

    protected function apiGet(string $uri)
    {
        return $this->api->request('GET', $uri);
    }
}
