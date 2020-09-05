Sample codes of LINE Notify
===========================

<img src="https://raw.githubusercontent.com/yidas/line-notify-sdk-php/master/img/sample-index-desktop.png" height="500" /><img src="https://raw.githubusercontent.com/yidas/line-notify-sdk-php/master/img/sample-index-mobile.png" height="500" />

FEATURES
--------

*1. **No database** required.*

*2. **Saving config with authentication** by session.*

*3. Support sending notification with **multiple accessTokens***

*4. Independent program files such as **Authorize, Refirect-url, Notify**.*

---

INSTALLATION
------------

Download repository and run Composer install in your Web directory: 

```
git clone https://github.com/yidas/line-notify-sdk-php.git;
cd line-notify-sdk-php;
composer install;
```

Then you can access the sample site from `https://{yourweb-dir}/line-notify-sdk-php/sample`.

> Set the callback URL in LINE Notify service to: `https://{yourweb-dir}/line-notify-sdk-php/sample/redirect-url.php`

---


CLINET SETTING
--------------

You can save your favorite LINE Notify client for sample page.

To enable the setting, create `sample/_credential.php` file (Under `sample` folder) using the following PHP array format:


```php
<?php

return [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
];
```
