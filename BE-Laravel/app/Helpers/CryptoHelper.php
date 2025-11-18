<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;

class CryptoHelper
{
    public static function decryptAES($encrypted, $secretKey)
    {
        try {
            $encrypted = base64_decode($encrypted);
            $salt = substr($encrypted, 8, 8);
            $encryptedData = substr($encrypted, 16);

            $keyAndIV = self::evpKDF($secretKey, $salt);

            $key = substr($keyAndIV, 0, 32);
            $iv = substr($keyAndIV, 32, 16);

            return openssl_decrypt(
                $encryptedData,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
        } catch (Exception $e) {
            Log::error("AES decrypt error: " . $e->getMessage());
            return null;
        }
    }

    private static function evpKDF($password, $salt)
    {
        $data = '';
        $key = '';

        while (strlen($key) < 48) {
            $data = md5($data . $password . $salt, true);
            $key .= $data;
        }

        return $key;
    }
}
