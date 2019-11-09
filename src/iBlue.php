<?php

namespace Evo\iBlue;

use Evo\iBlue\Authentication\AccessToken;
use Exception;
use GuzzleHttp\Client;

class iBlue
{

    /**
     * @const string The base authorization URL
     */
    const BASE_URL = "http://localhost:8001";

    /**
     * @const string The api version
     */
    const API_VERSION = "v1";

    /** 
     * @var string The app ID
     */
    private $appId;

    /** 
     * @var string The app secret
     */
    private $appSecret;

    /** 
     * @var string The redirect URL
     */
    private $redirectUrl;

    /**
     * Instantiates a new iBlue object
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct(string $appId = null, string $appSecret = null)
    {
        isset($appId) && $this->appId = $appId;
        isset($appSecret) && $this->appSecret = $appSecret;
    }

    /**
     * Returns the app id
     *
     * @return string|null
     */
    public function getAppId(): ?string
    {
        return $this->appId;
    }

    /**
     * Setter for the app id
     *
     * @param string $appId
     * @return self
     */
    public function setAppId(string $appId): self
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * Return the app secret
     *
     * @return string|null
     */
    public function getAppSecret(): ?string
    {
        return $this->appSecret;
    }

    /**
     * Setter for the app secret
     *
     * @param string $appSecret
     * @return self
     */
    public function setAppSecret(string $appSecret): self
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    /**
     * Returns the redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * Setter for the redirect url
     *
     * @param string $redirectUrl
     * @return self
     */
    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Return the authorization url
     *
     * @return string|null
     * @throws iBlueException
     */
    public function getAuthorizationUrl(): ?string
    {
        if (!$this->getAppId()) {
            throw new iBlueException("App ID is missing");
        }

        if (!$this->getAppSecret()) {
            throw new iBlueException("App secret is missing");
        }

        if (!$this->getRedirectUrl()) {
            throw new iBlueException("Redirect url is missing");
        }

        $parameters = http_build_query([
            'app_id' => $this->getAppId(),
            'redirect_url' => $this->getRedirectUrl()
        ]);

        $authorizationUrl = $this->buildUrl('/oauth', $parameters);

        return $authorizationUrl;
    }

    /**
     * Generates an URL 
     *
     * @param string $endpoint
     * @param string|null $parameters
     * @return string
     */
    protected function buildUrl(string $endpoint, ?string $parameters = null): string
    {
        $url = self::BASE_URL . $endpoint . "?" . $parameters;
        return $url;
    }

    /**
     * Returns an access token from code
     *
     * @param string $code
     * @return AccessToken
     */
    public function getAccessTokenFromCode(string $code)
    {
        $parameters = [
            'code' => $code
        ];

        return $this->requestAnAccessToken($parameters);
    }

    /**
     * Sends request for an access token
     *
     * @param array $parameters
     * @return AccessToken
     * @return iBlueException
     */
    protected function requestAnAccessToken(array $parameters)
    {

        // print_r(file_get_contents("http://localhost:8001/auth/token"));
        $data = $this->sendRequest('/auth/token', $parameters);
        print_r($data);

        if (isset($data->error)) {
            throw new iBlueException($data->error, $data->code);
        }

        if (!isset($data->access_token)) {
            throw new iBlueException("Access token was not returned", 401);
        }

        $expiresAt = 0; 
        
        if (isset($data->expires_in)) {
            $expiresAt = time() + $data->expires_in;
        } elseif(isset($data->expires_at)) {
            $expiresAt = $data->expires_at;
        }

        // print_r($data);

        // return new AccessToken($data->access_token, $expiresAt);
    }

    /**
     * Sends request 
     * @param string $endpoint
     * @param array $parameters
     * @return void
     */
    protected function sendRequest(string $endpoint, array $parameters)
    {

        $guzzleGlient = new Client([
            'timeout' => 2
        ]);

        $query_parameters = http_build_query($parameters);
        $url = $this->buildUrl($endpoint, $query_parameters);

        try {

            $response =  $guzzleGlient->get($url); 
            return $this->json_decode($response->getBody());

        } catch (\Exception $e) {

            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Sends request with the app id and the app secret
     *
     * @param string $endpoint
     * @param array $parameters
     * @return void
     */
    protected function sendRequestWithClientParameters(string $endpoint, array $parameters)
    {
        $parameters += [
            'app_id' => $this->getAppId(),
            'app_secret' => $this->getAppSecret()
        ];

        $guzzleGlient = new Client([
            'timeout' => 2
        ]);

        $query_parameters = http_build_query($parameters);
        $url = $this->buildUrl($endpoint, $query_parameters);

        try {

            $response =  $guzzleGlient->get($url); 
            return $this->json_decode($response->getBody());
        } catch (\Exception $e) {

            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Encode data as JSON
     *
     * @param mixed $data
     * @return json
     */
    public function json_decode($data)
    {
        return json_decode($data);
    }
}
