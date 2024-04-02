<?php

namespace holybunch\shared\security;

use holybunch\shared\exceptions\BadRequestException;

/**
 * The Encryption class provides methods for encrypting
 * and decrypting data using AES-256-CBC algorithm.
 */
class Encryption
{
    private const ALGO = 'aes-256-cbc';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Encrypts the given data using the specified key.
     *
     * @param string|int $data The data to encrypt.
     * @param string $key The encryption key.
     * @return string The encrypted data.
     * @throws BadRequestException If encryption fails.
     */
    public static function encrypt(string|int $data, string $key): string
    {
        $data = is_int($data) ? strval($data) : $data;
        $ocil = openssl_cipher_iv_length(self::ALGO);
        if (!$ocil) {
            // @codeCoverageIgnoreStart
            throw new BadRequestException("Invalid OpenSSL cipher IV length.");
            // @codeCoverageIgnoreEnd
        }
        $iv = openssl_random_pseudo_bytes($ocil);
        $encrypted = openssl_encrypt($data, self::ALGO, $key, 0, $iv);
        if (!$encrypted) {
            // @codeCoverageIgnoreStart
            throw new BadRequestException("Unable to encrypt the data.");
            // @codeCoverageIgnoreEnd
        }
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypts the given data using the specified key.
     *
     * @param string $data The data to decrypt.
     * @param string $key The encryption key.
     * @return string The decrypted data.
     * @throws BadRequestException If decryption fails or if the data is not valid base64 encoded.
     */
    public static function decrypt(string $data, string $key): string
    {
        $data = base64_decode($data);
        if (!$data) {
            throw new BadRequestException("Invalid base64 encoded data.");
        }
        $ocil = openssl_cipher_iv_length(self::ALGO);
        if (!$ocil) {
            // @codeCoverageIgnoreStart
            throw new BadRequestException("Invalid OpenSSL cipher IV length.");
            // @codeCoverageIgnoreEnd
        }
        $iv = substr($data, 0, $ocil);
        $decrypted = openssl_decrypt(substr($data, $ocil), self::ALGO, $key, 0, $iv);
        if (!$decrypted) {
            throw new BadRequestException("Unable to decrypt the data.");
        }
        return $decrypted;
    }
}
