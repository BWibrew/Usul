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

    public function discover(string $uri)
    {
        $response = $this->api->request('GET', $uri)->getHeader('Link')[0];

        return str_replace(['<', '>'], '', explode(';', $response)[0]);
    }
}
