<?php

use PHPUnit\Framework\TestCase;
use \yidas\lineNotify\Auth;

class OauthTest extends TestCase
{
    protected function getClient()
    {
        return new Auth([
            'clientId' => 'UnitTest-CLIENT-ID',
            'clientSecret' => 'UnitTest-CLIENT-SECRET',
            'log' => true,
        ]);
    }
    
    public function testClientConstructor()
    {
        $this->assertInstanceOf('\yidas\lineNotify\Auth', $this->getClient());
    }

    public function testGetAuthUrl()
    {
        $url = $this->getClient()->getAuthUrl("http://localhost/redirectUrl.php");
        $expected = 'https://notify-bot.line.me/oauth/authorize?response_type=code&client_id=UnitTest-CLIENT-ID&redirect_uri=http%3A%2F%2Flocalhost%2FredirectUrl.php&scope=notify&state=none';
        $this->assertEquals($url, $expected);
    }
    
    public function testToken()
    {
        $client = $this->getClient();
        $result = $client->getAccessToken("http://localhost/redirectUrl.php");
        $logs = $client->getResponseLogs();
        $response = end($logs);
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $bodyStatus = json_decode($body, true)['status'];

        $this->assertEquals($statusCode, 400);
        $this->assertEquals($bodyStatus, 400);
    }
}
