<?php

if (!class_exists('PHPUnit_Framework_TestCase') && class_exists('\PHPUnit\Framework\TestCase'))
    class_alias('\PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');

class jeniusConstructorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public static function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testSha256()
    {
        $hash = in_array('sha256', hash_algos());
        $this->assertTrue($hash);
    }

    public function testArrayImplode()
    {
        $params = array();
        $params['SearchBy'] = 'Distance';
        $params['Latitude'] = '123991239';
        $query = \Jenius\JeniusHttp::arrayImplode('=', '&', $params);
        $equal = 'SearchBy=Distance&Latitude=123991239';
        $this->assertEquals($equal, $query);
    }

    public function testArrayImplode2()
    {
        $params = array();
        $params['SearchBy'] = array('Distance' => 'Hellooooo');
        $params['Latitude'] = '123991239';
        $query = \Jenius\JeniusHttp::arrayImplode('=', '&', $params);
        $equal = 'SearchBy=Hellooooo&Latitude=123991239';
        $this->assertEquals($equal, $query);
    }

    /**
     * @expectedException \Jenius\JeniusHttpException
     */
    public function testArrayImplode3()
    {
        $query = \Jenius\JeniusHttp::arrayImplode('=', '&', 'q');
        $this->assertEquals('Data harus array.', $query);
    }

    /**
     * Testing jika array kosong.
     *
     * @expectedException \Jenius\JeniusHttpException
     */
    public function testArrayImplode4()
    {
        $query = \Jenius\JeniusHttp::arrayImplode('=', '&', array());
        $this->assertEquals('parameter array tidak boleh kosong.', $query);
    }

    public function testCurlOptionsCanBeSet()
    {
        $curl_opts = array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4);
        $options = array(
            'curl_options' => $curl_opts,
        );
        $jenius = new \Jenius\JeniusHttp('1234567', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($curl_opts, $settings['curl_options']);
    }

    /**
     * Testing constructor HOST.
     */
    public function testConstructHost1()
    {
        $equal = 'apidev.btpn.com';
        $jenius = new \Jenius\JeniusHttp('x_channel_id','client_id', 'secret', 'apikey', 'secret');
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['host']);
    }

    /**
     * Testing constructor HOST.
     */
    public function testConstructHost2()
    {
        $options = array();
        $options['host'] = 'xxxx.com';
        $equal = 'xxxx.com';
        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['host']);
    }

    /**
     * Testing constructor Scheme.
     */
    public function testConstructScheme()
    {
        $options = array();
        $options['scheme'] = 'http';
        $equal = 'http';
        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['scheme']);
    }

    /**
     * Testing constructor client_id.
     */
    public function testClientIdParameter()
    {
        $options = array();
        $client_id = '1234567-1234-1234-1345-123456789123';
        $equal = '1234567-1234-1234-1345-123456789123';

        $jenius = new \Jenius\JeniusHttp('1234567', $client_id, '1234567-1234-1234-1345-123456789123','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['client_id']);
    }

    /**
     * Testing constructor client_secret.
     */
    public function testClientSecretParameter()
    {
        $options = array();
        $client_secret = '1234567-1234-1234-1345-123456789123';
        $equal = '1234567-1234-1234-1345-123456789123';

        $jenius = new \Jenius\JeniusHttp('1234567','client_id', $client_secret, 'apikey', 'secret', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['client_secret']);
    }

    /**
     * Testing constructor api_key.
     */
    public function testApiKeyParameter()
    {
        $options = array();
        $api_key = '1234567-1234-1234-1345-123456789123';
        $equal = '1234567-1234-1234-1345-123456789123';

        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $api_key, '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['api_key']);
    }

    /**
     * Testing constructor secret_key.
     */
    public function testSecretParameter()
    {
        $secret = '1234567-1234-1234-1345-123456789123';
        $equal = '1234567-1234-1234-1345-123456789123';

        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $secret);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['secret_key']);
    }

    /**
     * Testing set timezone.
     */
    public function testTimeZone()
    {
        \Jenius\JeniusHttp::setTimeZone('Asia/Singapore');
        $timezone = \Jenius\JeniusHttp::getTimeZone();

        $this->assertEquals(
            $timezone,
            'Asia/Singapore'
        );
    }

    /**
     * Testing set timezone.
     */
    public function testSetTimeout()
    {
        \Jenius\JeniusHttp::setTimeOut(80);
        $timeout = \Jenius\JeniusHttp::getTimeOut();

        $this->assertEquals(
            $timeout,
            80
        );
    }

    /**
     * Testing constructor HOST.
     */
    public function testConstructPort()
    {
        $options = array();
        $options['port'] = 443;
        $equal = 443;
        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['port']);
    }

    /**
     * Testing constructor HOST.
     */
    public function testConstructTimeout()
    {
        $options = array();
        $options['timeout'] = 60;
        $equal = 60;
        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $settings = $jenius->getSettings();
        $this->assertEquals($equal, $settings['timeout']);
    }

    /**
     * Testing Authentikasi.
     */
    public function testAuth()
    {
        $options = array();
        $jenius = new \Jenius\JeniusHttp('1234567', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);
        $response = $jenius->httpAuth();
        $this->assertEquals($response->code, 401);
    }

    /**
     * Testing Payment Request.
     */
    public function testPaymentRequest()
    {
        $options = array();
        $jenius = new \Jenius\JeniusHttp('1234567','1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', '1234567-1234-1234-1345-123456789123', $options);

        $token = "o7d8qCgfsHwRneFGTHdQsFcS5Obmd26O10iBFRi50Ve8Yb06Ju5xx";

        $response = $jenius->paymentRequest(
            $token,
            '50000.00',
            'any-cashtag',
            'any-promo-code',
            'https://run.mocky.io/v3/55277c4f-24cc-4a50-9540-a81f9934747d',
            'any purchase description',
            '2019-04-29T16:15:05',
            '00000001'
        );

        $this->assertEquals($response->code, 400);
    }

    /**
     * Testing constructor HOST.
     */
    public function testClientStaticHost()
    {
        $equal = 'xxxx.com';
        $jenius = \Jenius\JeniusHttp::setHostName('xxxx.com');
        $this->assertEquals($equal, \Jenius\JeniusHttp::getHostName());
    }

    /**
     * Testing constructor PORT.
     */
    public function testClientStaticPort()
    {
        $equal = 443;
        \Jenius\JeniusHttp::setPort(443);
        $this->assertEquals($equal, \Jenius\JeniusHttp::getPort());
    }

    /**
     * Testing set scheme.
     */
    public function testScheme()
    {
        \Jenius\JeniusHttp::setScheme('http');
        $scheme = \Jenius\JeniusHttp::getScheme();

        $this->assertEquals(
            $scheme,
            'http'
        );
    }

    /**
     * Testing set scheme.
     */
    public function testCurl()
    {
        $curl_opts = array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4);
        \Jenius\JeniusHttp::setCurlOptions($curl_opts);
        $scheme = \Jenius\JeniusHttp::getCurlOptions();
        $this->assertEquals(
            $scheme,
            array(
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 60
            )
        );
    }
}
