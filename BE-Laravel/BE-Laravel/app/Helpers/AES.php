<?php

namespace App\Helpers;

use phpseclib3\Crypt\AES;

class AESHelper
{
    private static $secretKey = 'MY_SUPER_SECRET_KEY_256';

    public static function decrypt($encrypted)
    {
        $aes = new AES('cbc');
        $aes->setKey(self::$secretKey);

        $data = base64_decode($encrypted);
        $salt = substr($data, 8, 8);
        $ciphertext = substr($data, 16);

        $key = hash('sha256', self::$secretKey . $salt, true);
        $iv = substr(hash('sha256', $salt . self::$secretKey, true), 0, 16);

        return openssl_decrypt($ciphertext, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    }
}
