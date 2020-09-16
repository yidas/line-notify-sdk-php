<?php

use PHPUnit\Framework\TestCase;
use \yidas\lineNotify\Notify;

class NotifyTest extends TestCase
{
    protected function getClient()
    {
        return new Notify('UnitTest-ACCESSTO', [
            'log' => true,
        ]);
    }

    protected function checkClientResponse($client, $expectedCode=401)
    {
        $logs = $client->getResponseLogs();
        $response = end($logs);
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $bodyStatus = json_decode($body, true)['status'];

        $this->assertEquals($statusCode, 401);
        $this->assertEquals($bodyStatus, 401);
    }
    
    public function testClientConstructor()
    {
        $this->assertInstanceOf('\yidas\lineNotify\Notify', $this->getClient());
    }

    public function testNotify()
    {
        $client = $this->getClient();
        $result = $client->notify("Hello");
        $this->checkClientResponse($client);
    }

    public function testStatus()
    {
        $client = $this->getClient();
        $result = $client->status("UnitTest-ACCESSTO");
        $this->checkClientResponse($client);
    }

    public function testReovke()
    {
        $client = $this->getClient();
        $result = $client->revoke("UnitTest-ACCESSTO");
        $this->checkClientResponse($client);
    }

    public function testNotifyMultiple()
    {
        $client = $this->getClient();
        $client->setAccessTokens([
            'mock1',
            'mock2',
        ]);
        $result = $client->notify("Hello");
        $logs = $client->getResponseLogs();

        // First log
        $response = end($logs);
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $bodyStatus = json_decode($body, true)['status'];
        $this->assertEquals($statusCode, 401);
        $this->assertEquals($bodyStatus, 401);

        // Second log
        $response = prev($logs);
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $bodyStatus = json_decode($body, true)['status'];
        $this->assertEquals($statusCode, 401);
        $this->assertEquals($bodyStatus, 401);
    }
}
