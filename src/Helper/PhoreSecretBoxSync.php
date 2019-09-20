<?php


namespace Phore\Core\Helper;


class PhoreSecretBoxSync
{
    private $encryptionSecret;
    private $ttl;
    private $gzip;


    public function __construct($enryptionSecret, int $ttl=null, bool $gzip=true)
    {
        if ( ! is_string($enryptionSecret))
            throw new \InvalidArgumentException("Parameter1: Secret is not a string.");
        
        if ( ! function_exists("sodium_crypto_secretbox"))
            throw new \InvalidArgumentException("libsodium library (libsodium-ext) missing");

        if (strlen($enryptionSecret) < 8)
            throw new \InvalidArgumentException("Encryption secret minimum length is 8 bytes");

        $this->encryptionSecret = substr(
            sha1($enryptionSecret, true) . sha1($enryptionSecret . "P", true),
            0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES
        );
        $this->ttl = $ttl;
        $this->gzip = $gzip;
    }


    public function encrypt(string $plainData)
    {

        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $validTillTs = null;
        if ($this->ttl !== null)
            $validTillTs = time() + $this->ttl;

        $plainData = phore_json_encode([$validTillTs, $plainData, phore_hash($this->encryptionSecret . $validTillTs . $plainData . $nonce, false)]);

        if ($this->gzip) {
            $plainData = gzencode($plainData, 7);
        }

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $plainData,
                $nonce,
                $this->encryptionSecret
            )
        );
        sodium_memzero($plainData);
        $cipher = "E1-" . $cipher;
        return $cipher;
    }

    public function decrypt(string $encrypted) : ?string
    {

        if ( ! substr($encrypted, 0, 3) === "E1-")
            throw new \InvalidArgumentException("Decryption failed. Message has invalid E1 encrypted data prefix.");
        $encrypted = substr($encrypted, 3);

        $decoded = base64_decode($encrypted);

        // check for general failures
        if ($decoded === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 enconding.');
        }



        // check for incomplete message. CRYPTO_SECRETBOX_MACBYTES doesn't seem to exist in this version...
        if (!defined('CRYPTO_SECRETBOX_MACBYTES')) define('CRYPTO_SECRETBOX_MACBYTES', 16);
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + CRYPTO_SECRETBOX_MACBYTES)) {
            throw new \Exception('Decryption failed: mac/nonce ');
        }

        // pull nonce and ciphertext out of unpacked message
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        // decrypt it and account for extra padding from $block_size (enforce 512 byte limit)
        $message = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->encryptionSecret);

        // check for encrpytion failures
        if ($message === false) {
             throw new \InvalidArgumentException('Decryption failed. Incorrect secret.');
        }

        if ($this->gzip) {
            $message = gzdecode($message);
            if ($message === false)
                throw new \InvalidArgumentException("Cannot gzdecode message.");
        }
        $message = phore_json_decode($message);

        if (phore_hash($this->encryptionSecret . $message[0] . $message[1] . $nonce) !== $message[2])
            throw new \InvalidArgumentException("Hash mismatch.");

        if ($message[0] !== null && $message[0] > time() + $this->ttl)
            throw new \InvalidArgumentException("Message expires way to far in the future. (Limited to ttl)");

        if ($message[0] !== null && $message[0] < time())
            return null;

        return $message[1];
    }
}
