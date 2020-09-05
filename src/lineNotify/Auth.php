<?php

namespace yidas\lineNotify;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

/**
 * LINE Notify Client for Authentication
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @version 1.0.0
 */
class Auth extends BaseClient
{
    /**
     * LINE Notify API URL list
     *
     * @var array
     */
    protected static $apiUrls = [
        'authorize' => 'https://notify-bot.line.me/oauth/authorize',
        'token' => 'https://notify-bot.line.me/oauth/token',
    ];

    /**
     * Saved LINE Notify service's Client Id
     *
     * @var string
     */
    protected $clientId;

    /**
     * Saved LINE Notify service's Client Secret
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Constructor
     *
     * @param array $optParams API Key or option parameters
     *  'clientID' => LINE Notify service's clientID
     *  'clientSecret' => LINE Notify service's clientSecret
     *  'debug' => Debug mode: Throw error exception when API request or result fails
     *  'log' => Log mode: Save all responses to each API request
     * @return self
     */
    function __construct($optParams) 
    {
        // Assignment
        $clientId = isset($optParams['clientId']) ? $optParams['clientId'] : null;
        $clientSecret = isset($optParams['clientSecret']) ? $optParams['clientSecret'] : null;

        // Check
        if (!$clientId || !$clientSecret) {
            throw new Exception("clientId/clientSecret are required", 400);
        }

        // Save credential
        $this->clientId = (string) $clientId;
        $this->clientSecret = (string) $clientSecret;

        return parent::__construct($optParams);
    }

    /**
     * Get LINE Notify OAuth authorize URL
     *
     * @param string $redirectUrl Default is current URL
     * @param string $state CSRF token
     * @param string $responseMode
     * @return string OAuth authorize URL
     */
    public function getAuthUrl($redirectUrl=null, $state='none', $responseMode=null)
    {
        // RedirectUrl
        $redirectUrl = ($redirectUrl) ? $redirectUrl : self::getCurrentUrl();
        
        $query = [
            'response_type' => "code",
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUrl,
            'scope' => "notify",
            'state' => $state,
            'response_mode' => $responseMode,
        ];
        $redirectUrl = self::$apiUrls['authorize'] . '?' . http_build_query($query);

        return $redirectUrl;
    }

    /**
     * Get AccessToken with redirectUrl and callback's code
     *
     * @param string $redirectUri Default is getting current URL without query string
     * @param string $code Default is getting current URL's code from query string
     * @param boolean $stateForVerify Use when $code is the default
     * @return string AccessToken
     */
    public function getAccessToken($redirectUri=false, $code=false, $stateForVerify=false)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => ($code) ? $code : self::getCode($stateForVerify),
            'redirect_uri' => ($redirectUri) ? $redirectUri : preg_split( "/(\?|\&)code=/", self::getCurrentUrl())[0],
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        
        $response = $this->httpClient->request('POST', self::$apiUrls['token'], [
            'form_params' => $params,
        ]);

        $data = $this->responseHandler($response);
        return isset($data['access_token']) ? $data['access_token'] : false;
    }

    /**
     * Get code on callback redirect URL
     *
     * @param string $stateForVerify
     * @return string Code
     */
    static public function getCode($stateForVerify=false)
    {
        $code = isset($_GET['code']) ? $_GET['code'] : false;
        $state = isset($_GET['state']) ? $_GET['state'] : false;

        if ($stateForVerify && $stateForVerify != $state) {
            return false;
        }

        return $code;
    }

    /**
     * Get current web URL
     *
     * @param boolean $documentUri Return with PHP script URI
     * @return string
     */
    static public function getCurrentUrl($documentUri=false)
    {
        $uri = ($documentUri) ? $_SERVER['DOCUMENT_URI'] : $_SERVER['REQUEST_URI'];
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$uri}";
    }

    /**
     * Get current web URL path
     *
     * @return string
     */
    static public function getWebPath()
    {
        return dirname(self::getCurrentUrl(true));
    }
}