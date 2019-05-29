<?php


namespace Phore\Core\Helper;


class PhoreEncrypt
{
    private $encryptionSecret;
    private $padLen;
    private $gzip;

    const DEFAULT_PAD_SIZE = 1024;

    public function __construct(string $enryptionSecret, int $padLen=64, bool $gzip=true)
    {
    }


    public function encrypt(string $plainData, int $validTillTs=null)
    {
        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $plainData = phore_json_encode([$validTillTs, $plainData]);

        if ($this->gzip) {
            $plainData = gzencode($plainData, 7);
        }

        $plainData = sodium_pad($message, self::DEFAULT_PAD_SIZE);


        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $plainData,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    public function decrypt(string $enrypted) : ?string
    {
        $decoded = base64_decode($encrypted);

        // check for general failures
        if ($decoded === false) {
            throw new \Exception('The encoding failed');
        }

        // check for incomplete message. CRYPTO_SECRETBOX_MACBYTES doesn't seem to exist in this version...
        if (!defined('CRYPTO_SECRETBOX_MACBYTES')) define('CRYPTO_SECRETBOX_MACBYTES', 16);
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + CRYPTO_SECRETBOX_MACBYTES)) {
            throw new \Exception('The message was truncated');
        }

        // pull nonce and ciphertext out of unpacked message
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        // decrypt it and account for extra padding from $block_size (enforce 512 byte limit)
        $decrypted_padded_message = sodium_crypto_secretbox_open($ciphertext, $nonce, $secret_key);
        $message = sodium_unpad($decrypted_padded_message, self::DEFAULT_PAD_SIZE);

        // check for encrpytion failures
        if ($message === false) {
             throw new \Exception('The message was tampered with in transit');
        }

        if ($this->gzip) {
            $message = gzdecode($message);
            if ($message === false)
                throw new \InvalidArgumentException("Cannot gzdecode message.");
        }
        $message = phore_json_decode($message);

        if ($message[0] < time())
            return null;

        return $message[1];

        // cleanup
        sodium_memzero($ciphertext);
        sodium_memzero($secret_key);

        return $message;
    }
}
