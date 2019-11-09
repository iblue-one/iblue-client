<?php

namespace iBlue;

class iBlueClient
{

    protected $appId;

    protected $appSecret;

    protected $accessToken;

    protected $redirectUrl;

    const OAUTH2_API_ROOT = "http://localhost:8001/oauth/v2/";

    const API_ROOT = "http://localhost:8001/api/v1";

    protected $apiRoot = self::API_ROOT;

    protected $oAuthApiRoot = self::OAUTH2_API_ROOT;

    protected $apiHeaders = [
        "Content-Type" => "application/json"
    ];

    public function __construct(string $appId, string $appSecret)
    {
        !empty($cliendId) && $this->setAppId($appId);
        !empty($appSecret) && $this->setAppSecret($appSecret);
    }

    public function setClienId(string $appId)
    {
        $this->appId = $appId;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    
    public function setAppSecret(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }
    
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    public function setRedirectUrl(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getLoginUrl(){
        $params = [
            "app_id" => $this->getAppId(),
            "redirect_uri" => $this->getRedirectUrl()
        ];

        $uri = $this->buildUrl('auth', $params);

        return $uri;
    }

    public function buildUrl(string $endpoint, array $params){
        $url = $this->getOAuthApiRoot();
        $scheme = parse_url($url, PHP_URL_SCHEME);
    }

}
