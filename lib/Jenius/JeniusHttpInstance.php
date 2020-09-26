<?php

namespace Jenius;

class JeniusHttpInstance
{
    private static $instance = null;
    private static $x_channel_id = '';
    private static $client_id = '';
    private static $client_secret = '';
    private static $api_key = '';
    private static $secret_key = '';
    private static $options = array(
        'scheme' => 'https',
        'port' => 443,
        'timezone' => 'Asia/Jakarta',
        'timeout' => null,
        'development' => true,
    );

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getJeniusHttp()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = new JeniusHttp(
            self::$x_channel_id,
            self::$client_id,
            self::$client_secret,
            self::$api_key,
            self::$secret_key,
            self::$options
        );
        return self::$instance;
    }
}
