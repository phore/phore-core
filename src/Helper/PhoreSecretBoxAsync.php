<?php


namespace Phore\Core\Helper;

use stdClass;

class PhoreSecretBoxAsync
{
    public function __construct()
    {

    }

    public function createKeyPair()
    {
        $keyObj=new stdClass();
        $keypair = sodium_crypto_box_keypair();
        $keyObj->public_key = base64_encode(sodium_crypto_box_publickey($keypair));
        $keyObj->private_key = base64_encode(sodium_crypto_box_secretkey($keypair));
        return json_encode($keyObj);
    }


    public function encrypt(string $plainData, $public_key)
    {
        $encrypted_text = sodium_crypto_box_seal($plainData, base64_decode($public_key));
        return base64_encode($encrypted_text);
    }

    public function decrypt(string $secret, $public_key, $private_key)
    {
        $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(base64_decode($private_key), base64_decode($public_key));
        $decrypted_text = sodium_crypto_box_seal_open(base64_decode($secret), $keypair);
        return $decrypted_text;
    }
}
