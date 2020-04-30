<?php


namespace Phore\Core\Helper;

use stdClass;

class PhoreSecretBoxAsync
{
    private $ttl;
    private $gzip;

    public function __construct(int $ttl=null, bool $gzip=true)
    {       
        if ( ! function_exists("sodium_crypto_secretbox"))
            throw new \InvalidArgumentException("libsodium library (libsodium-ext) missing");

        $this->ttl = $ttl;
        $this->gzip = $gzip;
    }

    public function createKeyPair()
    {
        $keyObj=new stdClass();
        $keypair = sodium_crypto_box_keypair();
        $keyObj->public_key = base64_encode(sodium_crypto_box_publickey($keypair));
        $keyObj->private_key = base64_encode(sodium_crypto_box_secretkey($keypair));
        return json_encode($keyObj);
    }


    public function encrypt(string $plainData, string $public_key)
    {
        $public_key = base64_decode($public_key);

        $validTillTs = null;
        if ($this->ttl !== null) {
            $validTillTs = time() + $this->ttl;
        }
        $plainData = phore_json_encode([$validTillTs, $plainData, phore_hash($public_key . $validTillTs . $plainData, false)]);

        if ($this->gzip) {
            $plainData = gzencode($plainData, 7);
        }

        $cipher = base64_encode(
            sodium_crypto_box_seal($plainData, $public_key)
        );
        sodium_memzero($plainData);
        $cipher = "E1-" . $cipher;
        return $cipher;
    }

    public function decrypt(string $cipher, $public_key, $private_key)
    {
        if ( ! substr($cipher, 0, 3) === "E1-") {
            throw new \InvalidArgumentException("Decryption failed. Message has invalid E1 encrypted data prefix.");
        }
        $cipher = substr($cipher, 3);
        $cipher = base64_decode($cipher);
        $public_key = base64_decode($public_key);
        $private_key = base64_decode($private_key);
        // check for general failures
        if ($cipher === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 cipher enconding.');
        }
        if ($public_key === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 public key enconding.');
        }        
        if ($private_key === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 private key enconding.');
        }

        $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($private_key, $public_key);
        // check for incomplete message. CRYPTO_SECRETBOX_MACBYTES doesn't seem to exist in this version...
        if (!defined('CRYPTO_SECRETBOX_MACBYTES')) {
            define('CRYPTO_SECRETBOX_MACBYTES', 16);
        }
        if (mb_strlen($cipher, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + CRYPTO_SECRETBOX_MACBYTES)) {
            throw new \Exception('Decryption failed: mac/nonce ');
        }

        // decrypt it
        $message = sodium_crypto_box_seal_open($cipher, $keypair);

        // check for encrpytion failures
        if ($message === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect secret.');
        }

        if ($this->gzip) {
            $message = gzdecode($message);
        }
        if ($message === false) {
            throw new \InvalidArgumentException("Cannot gzdecode message.");
        }
        $message = phore_json_decode($message);

        if (phore_hash($public_key . $message[0] . $message[1]) !== $message[2]) {
            throw new \InvalidArgumentException("Hash mismatch.");
        }

        if ($message[0] !== null && $message[0] > time() + $this->ttl) {
            throw new \InvalidArgumentException("Message expires way to far in the future. (Limited to ttl)");
        }
        
        if ($message[0] !== null && $message[0] < time()) {
            return null;
        }

        return $message[1];
    }

}
