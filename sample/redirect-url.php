<?php

require __DIR__ . '/_config.php';

use yidas\lineNotify\Auth;
use yidas\lineNotify\Notify;

// Get last form data if exists
$config = isset($_SESSION['config']) ? $_SESSION['config'] : [];
// Session check
if (!$config) {
    die("<strong>ERROR:</strong> Session has invalided");
}

// LINE Notify SDK
$lineNotifyAuth = new Auth([
    'clientId' => $config['clientId'],
    'clientSecret' => $config['clientSecret'],
]);

$accessToken = $lineNotifyAuth->getAccessToken();
if (!$accessToken) {
    die("<script>alert('AccessToken obtain failed');history.back();</script>");
}

// Save AccessToken into session
$_SESSION['config']['accessTokens'][] = $accessToken;

$lineNotify = new Notify($accessToken);
$result = $lineNotify->notify('Obtain AccessToken Successfully');

header('Location: ' . './index.php' );