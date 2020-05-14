<?php


namespace Phore\Core\Helper;

use stdClass;

class PhoreSecretBoxAsync
{
    const PRECYPHER = "EA1-";
    private $ttl;
    private $gzip;

    public function __construct(int $ttl=null, bool $gzip=true)
    {       
        if ( ! function_exists("sodium_crypto_box_seal"))
            throw new \InvalidArgumentException("libsodium library (libsodium-ext) missing");

        $this->ttl = $ttl;
        $this->gzip = $gzip;
    }

    public function createKeyPair()
    {
        $keypair = sodium_crypto_box_keypair();
        return [
            "public_key" => base64_encode(sodium_crypto_box_publickey($keypair)),
            "private_key" => base64_encode(sodium_crypto_box_secretkey($keypair))
        ];
    }


    public function encrypt(string $plainData, string $public_key)
    {
        $public_key = base64_decode($public_key);
        if ($public_key === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 public key enconding.');
        } 

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
        $cipher = PhoreSecretBoxAsync::PRECYPHER . $cipher;
        return $cipher;
    }

    public function decrypt(string $cipher, string $private_key)
    {
        if ( ! substr($cipher, 0, 3) === PhoreSecretBoxAsync::PRECYPHER) {
            throw new \InvalidArgumentException("Decryption failed. Message has invalid EA1 encrypted data prefix.");
        }
        $cipher = substr($cipher, 3);
        $cipher = base64_decode($cipher);
        
        $private_key = base64_decode($private_key);
        // check for general failures
        if ($cipher === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 cipher enconding.');
        }
        if ($private_key === false) {
            throw new \InvalidArgumentException('Decryption failed. Incorrect base64 private key enconding.');
        }

        $public_key = sodium_crypto_box_publickey_from_secretkey($private_key);
        $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($private_key, $public_key);

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
