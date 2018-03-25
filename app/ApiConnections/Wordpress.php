<?php

namespace App\ApiConnections;

use Exception;
use GuzzleHttp\Client;

class Wordpress
{
    /**
     * URI v1 for 'WP Site Monitor' plugin.
     *
     * @const string
     */
    protected const WPSM_V1_URI = 'wp-site-monitor/v1/';

    /**
     * URI v1 for 'JWT Authentication for WP REST API' plugin.
     *
     * @const string
     */
    protected const JWT_V1_URI = 'jwt-auth/v1/token/';

    /**
     * Authentication token.
     *
     * @var string
     */
    protected $authToken = '';

    /**
     * Authentication type.
     *
     * @var string
     */
    protected $authType = null;

    /**
     * HTTP client to connect to remote site API.
     *
     * @var Client
     */
    protected $api;

    /**
     * Create a new WordPress API connection instance.
     *
     * @param Client $client
     */
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

    /**
     * @param string $uri
     *
     * @return string
     * @throws Exception
     */
    public function version(string $uri)
    {
        $response = $this->apiGet($uri.self::WPSM_V1_URI.'wp-version');

        $response = (string) $response->getBody();
        $response = json_decode($response, true);

        return is_string($response) ? $response : null;
    }

    /**
     * Authenticate with JSON web tokens.
     *
     * @param string $uri
     * @param array $parameters
     *
     * @return array
     */
    public function jwtAuth(string $uri, array $parameters)
    {
        $response = (string) $this->apiPost($uri.self::JWT_V1_URI, $parameters)->getBody();

        return json_decode($response, true);
    }

    /**
     * Get or set the authentication token.
     *
     * @param string|null $authToken
     *
     * @return string
     */
    public function authToken($authToken = null)
    {
        if ($authToken) {
            $this->authToken = $authToken;
        }

        return $this->authToken;
    }

    /**
     * Get or set the authentication type.
     *
     * @param string|null $authType
     *
     * @return string
     */
    public function authType($authType = null)
    {
        if ($authType) {
            $this->authType = $authType;
        }

        return $this->authType;
    }

    /**
     * Send GET request to API.
     *
     * @param string $uri
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function apiGet(string $uri)
    {
        return $this->api->request('GET', $uri, ['headers' => $this->getAuthHeaders()]);
    }

    /**
     * Send POST request to API.
     *
     * @param string $uri
     * @param array $parameters
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function apiPost(string $uri, array $parameters)
    {
        return $this->api->request('POST', $uri, ['json' => $parameters]);
    }

    /**
     * Get authentication headers.
     *
     * @return array
     */
    protected function getAuthHeaders()
    {
        switch (strtolower($this->authType)) {
            case 'jwt':
                return [
                    'Authorization' => 'Bearer '.$this->authToken,
                ];
            default:
                return [];
        }
    }
}
