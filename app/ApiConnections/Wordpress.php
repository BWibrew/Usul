<?php

namespace App\ApiConnections;

use GuzzleHttp\Client;

class Wordpress
{
    protected $api;

    public function __construct()
    {
        $this->api = new Client;
    }

    /**
     * Check if api is enabled and return root URI.
     *
     * @param string $uri
     *
     * @return mixed
     */
    public function discover(string $uri)
    {
        $response = $this->api->request('GET', $uri)->getHeader('Link');

        if (! count($response) > 0) {
            return;
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
        $response = (string) $this->api->request('GET', $uri)->getBody();

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
        $response = (string) $this->api->request('GET', $uri)->getBody();

        return json_decode($response, true)['name'];
    }
}
