[![Build Status](https://travis-ci.org/gobliggg/php-jenius.svg?branch=master)](https://travis-ci.org/gobliggg/php-jenius)
[![codecov](https://codecov.io/gh/gobliggg/php-jenius/branch/master/graph/badge.svg)](https://codecov.io/gh/gobliggg/php-jenius)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gobliggg/php-jenius/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gobliggg/php-jenius/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gobliggg/php-jenius/v/stable)](https://packagist.org/packages/gobliggg/php-jenius)
[![Latest Unstable Version](https://poser.pugx.org/gobliggg/php-jenius/v/unstable)](https://packagist.org/packages/gobliggg/php-jenius)
[![Total Downloads](https://poser.pugx.org/gobliggg/php-jenius/downloads)](https://packagist.org/packages/gobliggg/php-jenius)

# Jenius BTPN

Native PHP library untuk mengintegrasikan Aplikasi Anda dengan sistem Jenius BTPN. Untuk dokumentasi lebih jelas dan lengkap, silahkan kunjungi website resminya di [Developer Jenius](https://developers.btpn.com/api-documentation).

Jika merasa terbantu dengan adanya library ini, jangan lupa untuk kasih STAR untuk library ini.

## PHP Version Support

- [x] PHP 5.4.x
- [x] PHP 5.5.x
- [x] PHP 5.6.x
- [x] PHP 7.0.x
- [x] PHP 7.1.x
- [x] PHP 7.2.x
- [ ] PHP 7.3.x

Untuk lebih detail silahkan kunjungi [PHP Jenius TravisCI](https://travis-ci.org/gobliggg/php-jenius)

## Fitur Library

* [Installasi](https://github.com/gobliggg/php-jenius#instalasi)
* [Setting](https://github.com/gobliggg/php-jenius#koneksi-dan-setting)
* [Login](https://github.com/gobliggg/php-jenius#login)
* [Payment Request](https://github.com/gobliggg/php-jenius#payment-request)
* [Payment Status](https://github.com/gobliggg/php-jenius#payment-status)
* [Payment Refund](https://github.com/gobliggg/php-jenius#payment-refund)
* [How to contribute](https://github.com/gobliggg/php-jenius#how-to-contribute)

### INSTALASI

```bash
composer require "gobliggg/php-jenius"
```

### KONEKSI DAN SETTING

Sebelum masuk ke tahap ```LOGIN``` pastikan seluruh kebutuhan seperti ```X_CHANNEL_ID, CLIENT_KEY, CLIENT_SECRET, API_KEY, SECRET_KEY``` telah diketahui.

```php
    $options = array(
        'scheme'        => 'https',
        'port'          => 443,
        'host'          => 'apidev.btpn.com',
        'timezone'      => 'Asia/Jakarta',
        'timeout'       => 30,
        'debug'         => true,
        'development'   => true
    );

    // Setting default timezone Anda
    \Jenius\JeniusHttp::setTimeZone('Asia/Jakarta');

    // ATAU

    // \Jenius\JeniusHttp::setTimeZone('Asia/Singapore');

    $x_channel_id = "NILAI-X-CHANNEL-ID-ANDA";
    $client_key = "NILAI-CLIENT-KEY-ANDA";
    $client_secret = "NILAI-CLIENT-SECRET-ANDA";
    $apikey = "NILAI-APIKEY-ANDA";
    $secret = "SECRETKEY-ANDA";

    $jenius = new \Jenius\JeniusHttp($x_channel_id, $client_key, $client_secret, $apikey, $secret);

    // ATAU

    $jenius = new \Jenius\JeniusHttp($x_channel_id, $client_key, $client_secret, $apikey, $secret, $options);
```

Menggunakan custom **Curl Options**

```php
    $options = array(
        'curl_options'  => array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 60
        ),
        'scheme'        => 'https',
        'port'          => 443,
        'host'          => 'apidev.btpn.com',
        'timezone'      => 'Asia/Jakarta',
        'timeout'       => 30,
        'debug'         => true,
        'development'   => true
    );

    // Setting default timezone Anda
    \Jenius\JeniusHttp::setTimeZone('Asia/Jakarta');

    // ATAU

    // \Jenius\JeniusHttp::setTimeZone('Asia/Singapore');

    $x_channel_id = "NILAI-X-CHANNEL-ID-ANDA";
    $client_key = "NILAI-CLIENT-KEY-ANDA";
    $client_secret = "NILAI-CLIENT-SECRET-ANDA";
    $apikey = "NILAI-APIKEY-ANDA";
    $secret = "SECRETKEY-ANDA";

    $jenius = new \Jenius\JeniusHttp($x_channel_id, $client_key, $client_secret, $apikey, $secret, $options);
```

### LOGIN

```php
    $x_channel_id = "NILAI-X-CHANNEL-ID-ANDA";
    $client_key = "NILAI-CLIENT-KEY-ANDA";
    $client_secret = "NILAI-CLIENT-SECRET-ANDA";
    $apikey = "NILAI-APIKEY-ANDA";
    $secret = "SECRETKEY-ANDA";

    $jenius = new \Jenius\JeniusHttp($x_channel_id, $client_key, $client_secret, $apikey, $secret);

    // Request Login dan dapatkan nilai OAUTH
    $response = $jenius->httpAuth();

    // Cek hasil response berhasil atau tidak
    echo json_encode($response);
```

Setelah Login berhasil pastikan anda menyimpan nilai ```TOKEN``` di tempat yang aman, karena nilai ```TOKEN``` tersebut agar digunakan untuk tugas tugas berikutnya.

### PAYMENT REQUEST

Pastikan anda mendapatkan nilai ```TOKEN``` dan ```TOKEN``` tersebut masih berlaku (Tidak Expired).

```php
    // Ini adalah nilai token yang dihasilkan saat login
    $token = "MvXPqa5bQs5U09Bbn8uejBE79BjI3NNCwXrtMnjdu52heeZmw9oXgB";
    
    $amount = '50000';

    // Cashtag jenius btpn
    $cashTag = '$getha36';

    // Kode promo 
    $promoCode = '';

    // Url Callback ketika pembayaran sudah dilakukan, silahkan disesuaikan
    $urlCallback = "http://www.mocky.io/v2/5c7cdb361000009c14760c5b";

    // Deskripsi pembayaran, silahkan disesuaikan
    $purchaseDesc = "";

    // Tanggal transaksi anda
    $createdAt = "2020-09-26T21:14:07";
    
   // Nomor Transaksi anda, Silahkan generate sesuai kebutuhan anda
    $referenceNo = "";

    $response = $jenius->paymentRequest(
            $token,
            $amount,
            $cashTag,
            $promoCode,
            $urlCallback,
            $purchaseDesc,
            $createdAt,
            $referenceNo);

    // Cek hasil response berhasil atau tidak
    echo json_encode($response);
```

### PAYMENT STATUS

Pastikan anda mendapatkan nilai ```TOKEN``` dan ```TOKEN``` tersebut masih berlaku (Tidak Expired).

```php
    // Ini adalah nilai token yang dihasilkan saat login
    $token = "MvXPqa5bQs5U09Bbn8uejBE79BjI3NNCwXrtMnjdu52heeZmw9oXgB";

    // Nomor Transaksi anda, Silahkan generate sesuai kebutuhan anda
    $referenceNo = "";

    // Tanggal transaksi anda
    $createdAt = "2020-09-26T21:14:07";

    $response = $jenius->paymentStatus(
            $token,
            $referenceNo,
            $createdAt);

    // Cek hasil response berhasil atau tidak
    echo json_encode($response);
```

### PAYMENT REFUND

Pastikan anda mendapatkan nilai ```TOKEN``` dan ```TOKEN``` tersebut masih berlaku (Tidak Expired).

```php
    // Ini adalah nilai token yang dihasilkan saat login
    $token = "MvXPqa5bQs5U09Bbn8uejBE79BjI3NNCwXrtMnjdu52heeZmw9oXgB";

    // Ini adalah nilai approval code yang dihasilkan saat payment status
    $approvalCode = "";

    // Nomor Transaksi anda, Silahkan generate sesuai kebutuhan anda
    $referenceNo = "";

    $amount = "50000";

    // Tanggal transaksi anda
    $createdAt = "2020-09-26T21:14:07";

    $response = $jenius->paymentRefund(
            $token,
            $approvalCode,
            $referenceNo,
            $amount,
            $createdAt);

    // Cek hasil response berhasil atau tidak
    echo json_encode($response);
```

# TESTING

Untuk melakukan testing lakukan ```command``` berikut ini

```bash
composer run-script test
```

Atau menggunakan PHPUnit

```bash
vendor/bin/phpunit --verbose --coverage-text
```

# How to contribute

* Lakukan **FORK** code.
* Tambahkan **FORK** pada git remote anda

Untuk contoh commandline nya :

```bash
git remote add fork git@github.com:$USER/php-jenius.git  # Tambahkan fork pada remote, $USER adalah username GitHub anda
```

Misalkan :

```bash
git remote add fork git@github.com:johndoe/php-jenius.git
```

* Setelah FORK, buat feature ```branch``` baru dengan cara

```bash
git checkout -b feature/my-new-feature origin/develop 
```

* Lakukan pekerjaan pada repository anda tersebut. 
* Sebelum melakukan commit lakukan ```Reformat kode``` anda menggunakan sesuai [PSR-2 Coding Style Guide](https://github.com/gobliggg/php-jenius#guidelines)
* Setelah selesai lakukan commit

```bash
git commit -am 'Menambahkan fitur A..B..C..D'
```

* Lakukan ```Push``` ke branch yang telah dibuat

```bash
git push fork feature/my-new-feature
```

* Lakukan PullRequest pada GitHub, setelah pekerjaan anda akan kami review. Selesai.

## Guidelines

* Koding berstandart [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/)
* Pastikan seluruh test yang dilakukan telah pass, jika anda menambahkan fitur baru, anda diharus kan untuk membuat unit test terkait dengan fitur tersebut.
* Pergunakan [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) untuk menghindari conflict dan merge kode
* Jika anda menambahkan fitur, mungkin anda juga harus mengupdate halaman dokumentasi pada repository ini.

# LICENSE

MIT License

Copyright (c) 2019 gobliggg

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
