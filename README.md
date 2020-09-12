<p align="center">
    <a href="https://pay.line.me/" target="_blank">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/220px-LINE_logo.svg.png" width="100px">
    </a>
    <h1 align="center">LINE Notify SDK <i>for</i> PHP</h1>
    <br>
</p>

LINE Notify SDK for PHP

[![Latest Stable Version](https://poser.pugx.org/yidas/line-notify-sdk/v/stable?format=flat-square)](https://packagist.org/packages/yidas/line-notify-sdk)
[![License](https://poser.pugx.org/yidas/line-notify-sdk/license?format=flat-square)](https://packagist.org/packages/yidas/line-notify-sdk)


OUTLINE
-------

- [Demonstration](#demonstration)
- [Requirements](#requirements)
    - [Client for LINE Notify](#client-for-line-notify)
- [Installation](#installation)
- [Usage](#usage)
    - [Auth Client](#auth-client)
        - [getAuthUrl()](#getauthurl)
        - [getAccessToken()](#getaccesstoken)
        - [getCode()](#getcode)
        - [getWebPath()](#getwebpath)
    - [Notify Client](#notify-client)
        - [notify()](#notify)
        - [status()](#status)
        - [revoke()](#revoke)
        - [setAccessTokens()](#setaccesstokens)
        - [addAccessToken()](#addaccesstoken)
        - [getRateLimit()](#getratelimit)
    - [Shared Methods](#shared-methods)
        - [getResponseLogs()](#getresponselogs)
- [Resources](#resources)

---

DEMONSTRATION
-------------

[Sample Codes Site for LINE Notify](https://github.com/yidas/line-notify-sdk-php/tree/master/sample)

As a quick start, use `Auth` client to create an authorize URL with callback redirectUrl for redirection:

```php
$lineNotifyAuth = new \yidas\lineNotify\Auth([
    'clientId' => 'i5zOKhJ9hGyRYdCk281wJr',
    'clientSecret' => '************************',
]);

$authUrl = $$lineNotifyAuth->getAuthUrl("http://localhost/redirectUrl.php");
// header("Location: {$authUrl}");
```

Next, use `Auth` client to get accessToken on callback URL (`redirectUrl.php`), then use `Notify` client to send notifications with accessToken: 

```php
// Get accessToekn by automatically obtaining callback code from query string
$accessToken = $lineNotifyAuth->getAccessToken();

// Send notification with accessToken (Concurrency supported)
$lineNotify = new \yidas\lineNotify\Notify($accessToken);
$result = $lineNotify->notify('Hello!');
```

---


REQUIREMENTS
------------

This library requires the following:

- PHP 5.4.0+\|7.0+
- guzzlehttp/guzzle 5.3.1+\|6.0+
- [LINE Notify service client](#client-for-line-notify)

### Client for LINE Notify 

Each LINE Notify service require authentication information for integration, as shown below:
- Client ID
- Client Secret

To get a LINE Notify Client:
1. Register a new LINE Notify service from [LINE Notify - Add service](https://notify-bot.line.me/my/services/new) with redirectUrl setting.
2. After registering, get your service's **clientId/clientSecret** from [LINE Notify - Manage registered services (service provider)](https://notify-bot.line.me/my/services/) for integration.


---

INSTALLATION
------------

Run Composer in your project:

    composer require yidas/line-notify-sdk ~1.0.0
    
Then you could use SDK class after Composer is loaded on your PHP project:

```php
require __DIR__ . '/vendor/autoload.php';

use yidas\lineNotify\Auth;
use yidas\lineNotify\Notify;
```

---

USAGE
-----

Before using any API methods, first you need to create a Client with configuration, then use the client to access LINE Notify API methods.

### Auth Client

Create a LINE Notify Auth Client with [API Authentication](#client-for-line-notify):

```php
$lineNotifyAuth = new \yidas\lineNotify\Auth([
    'clientId' => 'Your LINE Notify service's client ID',
    'clientSecret' => 'Your LINE Notify service's client Secret',
    // 'debug' => true,
    // 'log' => true,
]);
```

##### Parameters 

- `array $config`:

|Key            |Required|Type     |Default|Description|
|:--               |:--     |:--      |:--    |:--        |
|clientId          |Y       |string   |       |LINE Notify service's client ID|
|clientSecret      |Y       |string   |       |LINE Notify service's client Secret|
|debug             |N       |boolean  |false  |Debug mode: Throw error exception when API request or result fails|
|log               |N       |boolean  |false  |Log mode: Save all responses to each API request|

#### getAuthUrl()

Get LINE Notify OAuth authorize URL

```php
public string getAuthUrl(string $redirectUrl=null, string $state='none', string $responseMode=null)
```

*Example:*
```php
// Set redirectUrl to `/callback` from the same path of current URL
define('LINE_NOTIFY_REDIRECT_URL', \yidas\lineNotify\Auth::getWebPath() . "/callback");
$authUrl = $lineNotifyAuth->getAuthUrl(LINE_NOTIFY_REDIRECT_URL);
```

#### getAccessToken()

Get AccessToken with redirectUrl and callback's code

```php
public string getAccessToken(string $redirectUri=false, string $code=false, boolean $stateForVerify=false)
```

*Example:*
```php
$accessToken = $lineNotifyAuth->getAccessToken(LINE_NOTIFY_REDIRECT_URL, $_GET['code'], 'CSRF state for verifying');
```

#### getCode()

Get code on callback redirect URL

```php
static public string getCode(string $stateForVerify=false)
```

#### getWebPath()

Get current web URL path

```php
static public string getWebPath()
```

### Notify Client

Create a LINE Notify Client with accessToekn setting:

```php
$lineNotify = new \yidas\lineNotify\Notify('HkyggKbHymoS*****************sFuVfa0mlcBNPI', [
    // 'debug' => true,
    // 'log' => true,
]);
```

##### Parameters

- `string|array $accessTokens`: Support single or multiple accessTokens for notification
- `array $config`:

|Key            |Required|Type     |Default|Description|
|:--               |:--     |:--      |:--    |:--        |
|debug             |N       |boolean  |false  |Debug mode: Throw error exception when API request or result fails|
|log               |N       |boolean  |false  |Log mode: Save all responses to each API request|


#### notify()

Send notification concurrently based on accessToken(s)

```php
public integer notify(string $message, array $options=[], string|array $accessTokens=false)
```

> Return Values: Number of successful notifications

*Example:*
```php
// Send single notification with one accessToken
$lineNotify = new \yidas\lineNotify\Notify('HkyggKbHymoS*****************sFuVfa0mlcBNPI');
$result = $lineNotify->notify('Hello!');

// Send notification for multiple accessTokens concurrently
$lineNotify = new \yidas\lineNotify\Notify(['GymoS****', 'Afa0****']);
$sccessNum = $lineNotify->notify('Hello everyone!');
```    

##### Options

|Option            |Type     |Description|
|:--               |:--      |:--        |
|message           |string   |1000 characters max|
|imageThumbnail    |HTTP/HTTPS URL  |Maximum size of 240×240px JPEG|
|imageFullsize     |HTTP/HTTPS URL  |Maximum size of 2048×2048px JPEG|
|imageFile         |string   |Local file path |
|stickerPackageId  |number |Package ID. ([Sticker List](https://devdocs.line.me/files/sticker_list.pdf))|
|stickerId         |number |Sticker ID.|
|notificationDisabled |boolean |Deault is `false`|

   
*Example*   

```php
$lineNotify = new \yidas\lineNotify\Notify(['HkyggKbHymoS*****************sFuVfa0mlcBNPI']);

// Send notification with image URL options
$successNum = $lineNotify->notify(false, [
    'message' => 'Image Notify',
    'imageThumbnail'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/220px-LINE_logo.svg.png',
    'imageFullsize'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/440px-LINE_logo.svg.png',
    ]);
    
// Send notification with image upload options
$successNum = $lineNotify->notify(false, [
    'message' => 'Image Notify',
    'imageFile' => '/tmp/filename.png',
    ]);
    
// Send notification with sticker options
$successNum = $lineNotify->notify(false, [
    'message' => 'Sticker Notify',
    'stickerPackageId' => 1,
    'stickerId' => 100,
    ]);
```

> `imageFile` requires to be a file path of string type

#### status()

Check accessToken connection status

```php
public array status(string $accessToken)
```

*Example:*
```php
$response = $lineNotify->status('HkyggKbHymoS*****************sFuVfa0mlcBNPI');
$statusCode = $response['status'];
```  

#### revoke()

Revoke accessToken on the connected service side

```php
public  revoke(string $accessToken)
```

*Example:*
```php
$result = $lineNotify->revoke('HkyggKbHymoS*****************sFuVfa0mlcBNPI');
```

#### setAccessTokens()

Set AccessTokens for sending notification

```php
public self setAccessTokens(array $accessTokens=[])
```

#### addAccessToken()

Add an AccessToken for sending notification

```php
public self addAccessToken(string $accessToken)
```

#### getRateLimit()

Get last response's Rate Limit information

```php
public array getRateLimit()
```

### Shared Methods

#### getResponseLogs()

Get response logs when log mode is enabled

```php
public array getResponseLogs()
```

---


RESOURCES
---------

**[LINE Notify (EN)](https://notify-bot.line.me/en/)**

**[LINE Notify API Document (EN)](https://notify-bot.line.me/doc/en/)**



