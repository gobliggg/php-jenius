<?php

$dir = dirname(__FILE__);
$config_path = $dir.'/config.php';
if (file_exists($config_path) === true) {
    require_once $config_path;
} else {
    define('JENIUS_CLIENT_ID', getenv('JENIUS_CLIENT_ID'));
    define('JENIUS_CLIENT_SECRET', getenv('JENIUS_CLIENT_SECRET'));
    define('JENIUS_API_KEY', getenv('JENIUS_API_KEY'));
    define('JENIUS_SECRET_KEY', getenv('JENIUS_SECRET_KEY'));
    define('JENIUS_HOST', 'https://apidev.btpn.com:443');
}

require_once $dir . '/../lib/Jenius.php';
