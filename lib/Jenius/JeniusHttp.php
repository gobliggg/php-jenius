<?php

namespace Jenius;

use Carbon\Carbon;
use Unirest\Request;
use Unirest\Request\Body;
use Unirest\Response;

class JeniusHttp
{
    /**
     * Default Timezone.
     *
     * @var string
     */
    private static $timezone = 'Asia/Jakarta';

    /**
     * Default Jenius Port.
     *
     * @var int
     */
    private static $port = 443;

    /**
     * Default Jenius Host.
     *
     * @var string
     */
    private static $hostName = 'apidev.btpn.com';

    /**
     * Default Jenius Scheme.
     *
     * @var string
     */
    private static $scheme = 'https';

    /**
     * Default Jenius Segment.
     *
     * @var string
     */
    private static $segment = 'pay';

    /**
     * Timeout curl.
     *
     * @var int
     */
    private static $timeOut = 60;

    /**
     * Default Curl Options.
     *
     * @var int
     */
    private static $curlOptions = array(
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSLVERSION => 6,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 60
    );

    /**
     * Default Jenius Settings.
     *
     * @var array
     */
    protected $settings = array(
        'x_channel_id' => '',
        'client_id' => '',
        'client_secret' => '',
        'api_key' => '',
        'secret_key' => '',
        'curl_options' => array(),
        // Backward compatible
        'host' => 'apidev.btpn.com',
        'scheme' => 'https',
        'segment' => 'pay',
        'timeout' => 60,
        'port' => 443,
        // New Options
        'options' => array(
            'host' => 'apidev.btpn.com',
            'scheme' => 'https',
            'segment' => 'pay',
            'timeout' => 60,
            'port' => 443
        )
    );

    /**
     * Default Constructor.
     *
     * @param string $x_channel_id
     * @param string $client_id
     * @param string $client_secret
     * @param string $api_key
     * @param string $secret_key
     * @param array $options
     */
    public function __construct($x_channel_id, $client_id, $client_secret, $api_key, $secret_key, array $options = [])
    {
        // Required parameters.
        $this->settings['x-channel-id'] = $x_channel_id;
        $this->settings['client_id'] = $client_id;
        $this->settings['client_secret'] = $client_secret;
        $this->settings['api_key'] = $api_key;
        $this->settings['secret_key'] = $secret_key;
        $this->settings['host'] =
            preg_replace('/http[s]?\:\/\//', '', $this->settings['host'], 1);

        foreach ($options as $key => $value) {
            if (isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }

        // Setup optional scheme, if scheme is empty
        if (isset($options['scheme'])) {
            $this->settings['scheme'] = $options['scheme'];
            $this->settings['options']['scheme'] = $options['scheme'];
        } else {
            $this->settings['scheme'] = self::getScheme();
            $this->settings['options']['scheme'] = self::getScheme();
        }

        // Setup optional host, if host is empty
        if (isset($options['host'])) {
            $this->settings['host'] = $options['host'];
            $this->settings['options']['host'] = $options['host'];
        } else {
            $this->settings['host'] = self::getHostName();
            $this->settings['options']['host'] = self::getHostName();
        }

        // Setup optional segment, if segment is empty
        if (isset($options['segment'])) {
            $this->settings['segment'] = $options['segment'];
            $this->settings['options']['segment'] = $options['segment'];
        } else {
            $this->settings['segment'] = self::getSegment();
            $this->settings['options']['segment'] = self::getSegment();
        }

        // Setup optional port, if port is empty
        if (isset($options['port'])) {
            $this->settings['port'] = $options['port'];
            $this->settings['options']['port'] = $options['port'];
        } else {
            $this->settings['port'] = self::getPort();
            $this->settings['options']['port'] = self::getPort();
        }

        // Setup optional timeout, if timeout is empty
        if (isset($options['timeout'])) {
            $this->settings['timeout'] = $options['timeout'];
            $this->settings['options']['timeout'] = $options['timeout'];
        } else {
            $this->settings['timeout'] = self::getTimeOut();
            $this->settings['options']['timeout'] = self::getTimeOut();
        }

        // Set Default Curl Options.
        Request::curlOpts(self::$curlOptions);

        // Set custom curl options
        if (!empty($this->settings['curl_options'])) {
            $data = self::mergeCurlOptions(self::$curlOptions, $this->settings['curl_options']);
            Request::curlOpts($data);
        }
    }

    /**
     * Get Settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Build the ddn domain.
     * output = 'https://apidev.btpn.com:443'
     * scheme = http(s)
     * host = apidev.btpn.com
     * port = 80 ? 443
     *
     * @return string
     */
    private function ddnDomain()
    {
        return $this->settings['scheme'] . '://' . $this->settings['host'] . ':' . $this->settings['port'] . '/';
    }

    /**
     * Get Token
     *
     * @return Response
     */
    public function httpAuth()
    {
        $client_id = $this->settings['client_id'];
        $client_secret = $this->settings['client_secret'];

        $headerToken = base64_encode("$client_id:$client_secret");

        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'Authorization' => "Basic $headerToken"
        );

        $request_path = "oauth/token";
        $domain = $this->ddnDomain();
        $full_url = $domain . $request_path;

        $data = array('grant_type' => 'client_credentials');
        $body = Body::form($data);
        $response = Request::post($full_url, $headers, $body);

        return $response;
    }

    /**
     * Create payment request
     *
     * @param string $oauth_token
     * @param int $amount
     * @param $cashtag
     * @param $promoCode
     * @param $urlCallback
     * @param $purchageDesc
     * @param $createdAt
     * @param $referenceNo
     * @return Response
     */
    public function paymentRequest(
        $oauth_token,
        $amount,
        $cashtag,
        $promoCode,
        $urlCallback,
        $purchageDesc,
        $createdAt,
        $referenceNo
    ) {
        $uriSign = "POST:/" . $this->settings['segment'] . "/payrequest";
        $apiKey = $this->settings['api_key'];
        $apiSecret = $this->settings['secret_key'];

        $btpnTimestamp = self::generateBtpnTimestamp();
        $btpnOriginalTimestamp = self::generateOriginalTimestamp($createdAt);

        $headers = array();
        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['Authorization'] = "Bearer $oauth_token";
        $headers['BTPN-ApiKey'] = $apiKey;
        $headers['BTPN-Timestamp'] = $btpnTimestamp;
        $headers['X-Channel-Id'] = $this->settings['x-channel-id'];
        $headers['X-Node'] = 'Jenius Pay';
        $headers['X-Transmission-Date-Time'] = $btpnTimestamp;
        $headers['X-Original-Transmission-Date-Time'] = $btpnOriginalTimestamp;
        $headers['X-Reference-No'] = $referenceNo;

        $request_path = $this->settings['segment'] . "/payrequest";
        $domain = $this->ddnDomain();
        $full_url = $domain . $request_path;

        $bodyData = array();
        $bodyData['txn_amount'] = $amount;
        $bodyData['cashtag'] = $cashtag;
        $bodyData['promo_code'] = $promoCode;
        $bodyData['url_callback'] = $urlCallback;
        $bodyData['purchase_desc'] = $purchageDesc;

        $authSignature = self::generateSign($uriSign, $apiKey, $apiSecret, $btpnTimestamp, $bodyData);

        $headers['BTPN-Signature'] = $authSignature;

        $encoderData = json_encode($bodyData, JSON_UNESCAPED_SLASHES);

        $body = Body::form($encoderData);
        $response = Request::post($full_url, $headers, $body);

        return $response;
    }

    /**
     * Get payment status
     *
     * @param string $oauth_token
     * @param $referenceNo
     * @param $createdAt
     * @return Response
     */
    public function getPaymentStatus(
        $oauth_token,
        $referenceNo,
        $createdAt
    ) {
        $uriSign = "GET:/" . $this->settings['segment'] . "/paystatus";
        $apiKey = $this->settings['api_key'];
        $apiSecret = $this->settings['secret_key'];

        $btpnTimestamp = self::generateBtpnTimestamp();
        $btpnOriginalTimestamp = self::generateOriginalTimestamp($createdAt);

        $headers = array();
        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['Authorization'] = "Bearer $oauth_token";
        $headers['BTPN-ApiKey'] = $apiKey;
        $headers['BTPN-Timestamp'] = $btpnTimestamp;
        $headers['X-Channel-Id'] = $this->settings['x-channel-id'];
        $headers['X-Node'] = 'Jenius Pay';
        $headers['X-Transmission-Date-Time'] = $btpnTimestamp;
        $headers['X-Original-Transmission-Date-Time'] = $btpnOriginalTimestamp;
        $headers['X-Reference-No'] = $referenceNo;

        $request_path = $this->settings['segment'] . "/paystatus";
        $domain = $this->ddnDomain();
        $full_url = $domain . $request_path;

        $authSignature = self::generateSign($uriSign, $apiKey, $apiSecret, $btpnTimestamp, []);

        $headers['BTPN-Signature'] = $authSignature;

        $response = Request::get($full_url, $headers, null);

        return $response;
    }

    /**
     * Generate Signature.
     *
     * @param string $url Url yang akan disign.
     * @param $apiKey
     * @param $apiSecret
     * @param $btpnTimestamp
     * @param array|mixed $bodyToHash array Body yang akan dikirimkan ke Server Jenius.
     *
     * @return string
     */
    public static function generateSign($url, $apiKey, $apiSecret, $btpnTimestamp, $bodyToHash = [])
    {
        if (!empty($bodyToHash)) {
            $encoderData = json_encode($bodyToHash, JSON_UNESCAPED_SLASHES);
            $stringToSign = $url . ":" . $apiKey . ":" . $btpnTimestamp . ":" . $encoderData;
        } else {
            $stringToSign = $url . ":" . $apiKey . ":" . $btpnTimestamp;
        }

        $auth_signature = hash_hmac('sha256', $stringToSign, $apiSecret, true);
        return base64_encode($auth_signature);
    }

    /**
     * Set TimeZone.
     *
     * @param string $timeZone
     *
     * @return string
     */
    public static function setTimeZone($timeZone)
    {
        self::$timezone = $timeZone;
    }

    /**
     * Get TimeZone.
     *
     * @return string
     */
    public static function getTimeZone()
    {
        return self::$timezone;
    }

    /**
     * Set Jenius Hostname
     *
     * @param string $hostName
     *
     * @return string
     */
    public static function setHostName($hostName)
    {
        self::$hostName = $hostName;
    }

    /**
     * Get Jenius Hostname
     *
     * @return string
     */
    public static function getHostName()
    {
        return self::$hostName;
    }

    /**
     * Set Jenius Segment
     *
     * @param string $segment
     *
     * @return string
     */
    public static function setSegment($segment)
    {
        self::$segment = $segment;
    }

    /**
     * Get Jenius Segment
     *
     * @return string
     */
    public static function getSegment()
    {
        return self::$segment;
    }

    /**
     * Get Max Execution Time.
     *
     * @return string
     */
    public static function getTimeOut()
    {
        return self::$timeOut;
    }

    /**
     * Get Curl Options
     *
     * @return string
     */
    public static function getCurlOptions()
    {
        return self::$curlOptions;
    }

    /**
     * Setup Curl Options.
     *
     * @param array $curlOpts
     * @return void
     */
    public static function setCurlOptions(array $curlOpts = [])
    {
        $data = self::mergeCurlOptions(self::$curlOptions, $curlOpts);
        self::$curlOptions = $data;
    }

    /**
     * Set Max Execution Time
     *
     * @param int $timeOut
     *
     * @return string
     */
    public static function setTimeOut($timeOut)
    {
        self::$timeOut = $timeOut;
        return self::$timeOut;
    }

    /**
     * Set Jenius Port
     *
     * @param int $port
     *
     * @return void
     */
    public static function setPort($port)
    {
        self::$port = $port;
    }

    /**
     * Get Jenius Port
     *
     * @return int
     */
    public static function getPort()
    {
        return self::$port;
    }

    /**
     * Set Jenius Scheme
     *
     * @param int $scheme
     *
     * @return string
     */
    public static function setScheme($scheme)
    {
        self::$scheme = $scheme;
    }

    /**
     * Get Jenius Scheme
     *
     * @return string
     */
    public static function getScheme()
    {
        return self::$scheme;
    }

    /**
     * Generate ISO8601 Time.
     *
     * @return string
     */
    public static function generateBtpnTimestamp()
    {
        $date = Carbon::now(self::getTimeZone());
        date_default_timezone_set(self::getTimeZone());
        $fmt = $date->format('Y-m-d\TH:i:s');
        $ISO8601 = sprintf("$fmt.%s%s", substr(microtime(), 2, 3), date('P'));

        return $ISO8601;
    }

    // 2020-09-26T21:14:07
    public static function generateOriginalTimestamp($date)
    {
        $ISO8601 = sprintf("$date.%s%s", substr(microtime(), 2, 3), date('P'));
        return $ISO8601;
    }

    /**
     * Merge from existing array.
     *
     * @param array $existing_options
     * @param array $new_options
     * @return array
     */
    private static function mergeCurlOptions(&$existing_options, $new_options)
    {
        $existing_options = $new_options + $existing_options;
        return $existing_options;
    }

    /**
     * Implode an array with the key and value pair giving
     * a glue, a separator between pairs and the array
     * to implode.
     *
     * @param string $glue The glue between key and value
     * @param string $separator Separator between pairs
     * @param array $array The array to implode
     *
     * @return string The imploded array
     * @throws JeniusHttpException error
     */
    public static function arrayImplode($glue, $separator, $array = [])
    {
        if (!is_array($array)) {
            throw new JeniusHttpException('Data should array.');
        }
        if (empty($array)) {
            throw new JeniusHttpException('Parameter can`t be empty.');
        }
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $string[] = "{$key}{$glue}{$val}";
        }

        return implode($separator, $string);
    }
}
