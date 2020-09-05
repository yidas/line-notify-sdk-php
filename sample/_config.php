<?php
// Debug
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

// Composer
define("PATH_VENDOR", __DIR__ . '/../vendor/autoload.php');
// Check
if (!file_exists(PATH_VENDOR)) {
    die("Composer is not installed, please install Composer.");
}
require PATH_VENDOR;

/**
 * Merchant Helper
 */
class Credential
{
    private static $configPath = __DIR__ . "/_credential.php";

    /**
     * Get merchant list from config
     *
     * @return array
     */
    public static function get()
    {
        if (!file_exists(self::$configPath)) {
            return false;
        }
        
        $data = include self::$configPath;

        // Check format
        if (!$data['clientId'] || !$data['clientSecret']) {
            return null;
            // die("<strong>ERROR:</strong> Incorrect credential config format - Must contain `clientId` and `clientSecret`. (" . basename(self::$configPath) . ")");
        }

        return $data;
    }
}