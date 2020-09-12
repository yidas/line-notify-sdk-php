<?php

require __DIR__ . '/_config.php';

use yidas\lineNotify\Auth;

$input = $_POST;

// Saved Credential
$credential = Credential::get();
if ($credential) {
    $input['clientId'] = $credential['clientId'];
    $input['clientSecret'] = $credential['clientSecret'];
}

// LINE Notify SDK
$lineNotifyAuth = new Auth([
    'clientId' => $input['clientId'],
    'clientSecret' => $input['clientSecret'],
]);

$authUrl = $lineNotifyAuth->getAuthUrl(Auth::getWebPath() . "/redirect-url.php");

// Save input for next process and next form
$_SESSION['config'] = $input;
$_SESSION['config']['accessTokens'] = ($input['accessTokens'][0]) ? $input['accessTokens'] : [];

header('Location: ' . $authUrl);