<?php

require __DIR__ . '/_config.php';

use yidas\lineNotify\Notify;

$input = $_POST;

// Saved Credential
$credential = Credential::get();
if ($credential) {
    $input['clientId'] = $credential['clientId'];
    $input['clientSecret'] = $credential['clientSecret'];
}

// LINE Notify SDK
$lineNotify = new Notify([
    // 'debug' => true,
    // 'log' => true,
]);

$successNum = $lineNotify
    ->setAccessTokens($input['accessTokens'])
    ->notify($input['message'], [
        // 'message' => 'Image Notify',
        // 'imageThumbnail'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/220px-LINE_logo.svg.png',
        // 'imageFullsize'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/440px-LINE_logo.svg.png',
        // 'imageFile' => '/tmp/440px-LINE_logo.svg.png',
        // 'stickerPackageId' => 1,
        // 'stickerId' => 100,
        ]);

// Save input for next process and next form
$_SESSION['config'] = $input;
    
die("<script>alert('Success Notification: {$successNum}');history.back();</script>");