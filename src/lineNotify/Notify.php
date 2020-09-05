<?php

namespace yidas\lineNotify;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

/**
 * LINE Notify Client for Notification
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @version 1.0.0
 */
class Notify extends BaseClient
{
    /**
     * LINE Notify API URL list
     *
     * @var array
     */
    protected static $apiUrls = [
        'notify' => 'https://notify-api.line.me/api/notify',
        'status' => 'https://notify-api.line.me/api/status',
        'revoke' => 'https://notify-api.line.me/api/revoke',
    ];

    /**
     * LINE Notify API Rate Limit headers
     *
     * @var array
     */
    protected static $rateLimitHeaders = [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-ImageLimit',
        'X-RateLimit-ImageRemaining',
        'X-RateLimit-Reset',
    ];

    /**
     * Debug Mode
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     * Saved accessToken list to request for Notify API
     *
     * @var array
     */
    protected $accessTokens = [];

    /**
     * API Rate Limit of last response
     *
     * @param array
     */
    protected $lastRateLimit = null;

    /**
     * Constructor
     *
     * @param string|array $accessTokens Support single or multiple accessTokens for notification
     * @param array $optParams Option parameters
     *  'debug' => Debug mode: Throw error exception when API request or result fails
     *  'log' => Log mode: Save all responses to each API request
     * @return self
     */
    function __construct($accessTokens=null, $optParams=[]) 
    {
        // Assignment
        if ($accessTokens) {
            $this->setAccessTokens($accessTokens);
        }

        return parent::__construct($optParams);
    }

    /**
     * Send notification concurrently based on accessToken(s)
     *
     * @param string $message
     * @param array $options API request parameters array ('imageFile' requires to be a file path of string type)
     * @param string|array $accessToken Support single or multiple accessTokens for notification
     * @return integer Number of successful notifications
     */
    public function notify($message, $options=[], $accessTokens=false)
    {
        // AccessTokens assign
        $accessTokens = ($accessTokens) ? $accessTokens : $this->accessTokens;
        // Check
        if (!$accessTokens) {
            return false;
        }
        // Unify type 
        $accessTokens = is_array($accessTokens) ? $accessTokens : [$accessTokens];
        
        // Params
        $options = is_array($options) ? $options : [];
        $defaultOptions = [
            'message' => (string) $message,
            'imageThumbnail' => null,
            'imageFullsize' => null,
            'imageFile' => null,
            'stickerPackageId' => null,
            'stickerId' => null,
            'notificationDisabled' => null,
        ];
        $options = array_merge($defaultOptions, $options);
        $params = $options;

        // Result count
        $countSuccess = 0;

        // imageFile option with multipart/form-data requests (Synchronous)
        if ($params['imageFile']) {

            // Request per each
            foreach ($accessTokens as $key => $accessToken) {
                // Build Guzzle multipart params (File stream resource requires to re-fopen after every request)
                $multipart = [];
                foreach ($params as $key => $param) {
                    if (!$param) {
                        continue;
                    }
                    $current = & $multipart[];
                    $current['name'] = $key;
                    $current['contents'] = ($key=='imageFile') ? fopen($param, 'r') : $param;
                }
                // Request
                $response = $this->httpClient->request('POST', self::$apiUrls['notify'], [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                    ],
                    'multipart' => $multipart,
                ]);
                
                // Result handler
                $data = $this->responseHandler($response);
                $code = $data['status'];
                if ($code==200) {
                    $countSuccess ++;
                } 
                elseif ($this->debug) {
                    throw new Exception("Request failed: {$responseBody}", $code);
                }
            }
        }
        // Common requests (Asynchronous)
        else {

            // Requests
            $requests = [];
            foreach ($accessTokens as $key => $accessToken) {
                $requests[] = new Request('POST', self::$apiUrls['notify'], [
                    'Authorization' => "Bearer {$accessToken}",
                ], http_build_query($params));
            }

            // Pool requests
            $pool = new Pool($this->httpClient, $requests, [
                'concurrency' => count($requests),
                'fulfilled' => function (Response $response, $index) use (&$countSuccess) {
                    $data = $this->responseHandler($response);
                    $code = $data['status'];
                    if ($code==200) {
                        $countSuccess ++;
                    } 
                    elseif ($this->debug) {
                        throw new Exception("Request failed: {$responseBody}", $code);
                    }
                },
                'rejected' => function (RequestException $reason, $index) {
                    if ($this->debug) {
                        throw new Exception("Request rejected: {$responseBody}", 400);
                    }
                },
            ]);
            // Initiate the transfers and create a promise
            $promise = $pool->promise();
            // Force the pool of requests to complete.
            $promise->wait();
        }

        return $countSuccess;
    }

    /**
     * Set AccessTokens for sending notification
     *
     * @param string|array $accessTokens Support single or multiple accessTokens for notification
     * @return self
     */
    public function setAccessTokens($accessTokens=[])
    {
        if (empty($accessTokens)) {
            $this->accessTokens = [];
        } else {
            $this->accessTokens = is_array($accessTokens) ? $accessTokens : [$accessTokens];
        }
        
        return $this;
    }

    /**
     * Add an AccessToken for sending notification
     *
     * @param string $accessToken
     * @return self
     */
    public function addAccessToken($accessToken)
    {
        $this->accessTokens[] = (string) $accessToken;
        return $this;
    }

    /**
     * Check accessToken connection status
     *
     * @param string $accessToken
     * @return array Array of status object
     */
    public function status($accessToken)
    {
        // Request
        $response = $this->httpClient->request('GET', self::$apiUrls['status'], [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);

        $data = $this->responseHandler($response);
        return $data;
    }

    /**
     * Revoke accessToken on the connected service side
     *
     * @param string $accessToken
     * @return boolean Result
     */
    public function revoke($accessToken)
    {
        // Request
        $response = $this->httpClient->request('POST', self::$apiUrls['revoke'], [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);

        $data = $this->responseHandler($response);
        return isset($data['status']) && $data['status'] ? true : false;
    }

    /**
     * Get last response's Rate Limit information
     *
     * @return array
     */
    public function getRateLimit()
    {
        return $this->lastRateLimit;
    }

    /**
     * Log response data
     *
     * @param Response $response
     * @return void
     */
    protected function log(Response $response)
    {
        $headers = $response->getHeaders();
        // RateLimit handler
        foreach ($headers as $key => $value) {
            if (in_array($key, self::$rateLimitHeaders)) {
                $this->lastRateLimit[$key] = $value[0];
            }
        }

        return parent::log($response);
    }
}