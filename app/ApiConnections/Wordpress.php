<?php

namespace App\ApiConnections;

use GuzzleHttp\Client;

class Wordpress
{
    /**
     * URI v1 for 'WP Site Monitor' plugin.
     *
     * @const string
     */
    protected const WPSM_V1_URI = 'wp-site-monitor/v1';

    /**
     * URI v1 for 'JWT Authentication for WP REST API' plugin.
     *
     * @const string
     */
    protected const JWT_V1_URI = 'jwt-auth/v1/token';

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
     * Get or set the authentication token.
     *
     * @param string|null $authToken
     *
     * @return string
     */
    public function authToken($authToken = null): string
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
    public function authType($authType = null): string
    {
        if ($authType) {
            $this->authType = $authType;
        }

        return $this->authType;
    }

    /**
     * Check if api is enabled and return root URI.
     *
     * @param string $uri
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function discover(string $uri): string
    {
        $response = $this->apiGet($uri);
        $response = $response->getHeader('Link');

        if (! count($response) > 0) {
            return '';
        }

        return str_replace(['<', '>'], '', explode(';', $response[0])[0]);
    }

    /**
     * Get the API namespaces.
     *
     * @param string $uri
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function namespaces(string $uri): array
    {
        $response = $this->apiGet($uri)->getBody()->getContents();
        $response = json_decode($response, true);

        if (array_key_exists('namespaces', $response)) {
            return $response['namespaces'];
        }

        return [];
    }

    /**
     * Get the site name.
     *
     * @param string $uri
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function siteName(string $uri): string
    {
        $response = $this->apiGet($uri)->getBody()->getContents();
        $response = json_decode($response, true);

        return array_key_exists('name', $response) ? $response['name'] : '';
    }

    /**
     * Check for successful connection to remote API.
     *
     * @param string $uri
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function connectionStatus(string $uri): array
    {
        return [
            'connected' => $this->apiGet($uri)->getStatusCode() === 200,
            'authenticated' => $this->authenticationStatus($uri),
        ];
    }

    /**
     * Check authentication status.
     *
     * @param string $uri
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authenticationStatus(string $uri): bool
    {
        switch (strtolower($this->authType)) {
            case 'jwt':
                return $this->apiPost(
                    $uri.'/'.self::JWT_V1_URI.'/validate',
                    ['headers' => $this->getAuthHeaders()]
                )->getStatusCode() === 200;

                break;
        }

        return false;
    }

    /**
     * Get the WordPress version number.
     *
     * @param string $uri
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function version(string $uri): string
    {
        $response = $this->apiGet(
            $uri.'/'.self::WPSM_V1_URI.'/wp-version',
            ['headers' => $this->getAuthHeaders()]
        );
        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);

        return is_string($response) ? $response : 'Unknown';
    }

    /**
     * Authenticate with JSON web tokens.
     *
     * @param string $uri
     * @param array $credentials
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function jwtAuth(string $uri, array $credentials): array
    {
        $response = $this->apiPost($uri.'/'.self::JWT_V1_URI, ['json' => $credentials])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * Get list of installed plugins.
     *
     * @param string $uri
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function plugins(string $uri): array
    {
        $response = (string) $this->apiGet(
            $uri.'/'.self::WPSM_V1_URI.'/plugins',
            ['headers' => $this->getAuthHeaders()]
        )->getBody();

        return json_decode($response, true);
    }

    /**
     * Send GET request to API.
     *
     * @param string $uri
     *
     * @param array $parameters
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function apiGet(string $uri, array $parameters = [])
    {
        app('log')->debug('API GET', [
            'uri' => $uri,
            'parameters' => $parameters,
        ]);

        return $this->api->request('GET', $uri, $parameters);
    }

    /**
     * Send POST request to API.
     *
     * @param string $uri
     * @param array $parameters
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function apiPost(string $uri, array $parameters)
    {
        app('log')->debug('API POST', [
            'uri' => $uri,
            'parameters' => $parameters,
        ]);

        return $this->api->request('POST', $uri, $parameters);
    }

    /**
     * Get authentication headers.
     *
     * @return array
     */
    protected function getAuthHeaders(): array
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
