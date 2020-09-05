<?php

namespace yidas\lineNotify;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;

class BaseClient
{
    /**
     * HTTP Client
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Debug Mode
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     * Response logs
     *
     * @param array
     */
    protected $log = false;

    /**
     * Mode for logging each response
     *
     * @param array GuzzleHttp\Psr7\Response
     */
    protected $responseLogs = [];

    /**
     * Constructor
     *
     * @param array $optParams API Key or option parameters
     *  'debug' => Debug mode: Throw error exception when API request or result fails
     *  'log' => Log mode: Save all responses to each API request
     * @return self
     */
    function __construct($optParams) 
    {
        // Assignment
        $this->debug = isset($optParams['debug']) && $optParams['debug'] ? true : $this->debug;
        $this->log = isset($optParams['log']) && $optParams['log'] ? true : $this->log;

        // Headers
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];;

        // Load GuzzleHttp\Client
        $this->httpClient = new HttpClient([
            // 'timeout'  => 6.0,
            'headers' => $headers,
            'http_errors' => $this->debug,
        ]);

        return $this;
    }

    /**
     * Get response logs when log mode is enabled
     *
     * @return array
     */
    public function getResponseLogs()
    {
        return $this->responseLogs;
    }

    /**
     * Handle response and get format data
     *
     * @param Response $response
     * @return array Response body with JSON decode
     */
    protected function responseHandler(Response $response)
    {
        $this->log($response);
        // JSON decode for response body
        $data = json_decode($response->getBody()->getContents(), true);
        
        return $data;
    }

    /**
     * Log response data
     *
     * @param Response $response
     * @return void
     */
    protected function log(Response $response)
    {
        // Log mode
        if ($this->log) {
            // Save into logs array
            $this->responseLogs[] = $response;
        }
        
        return;
    }
}
