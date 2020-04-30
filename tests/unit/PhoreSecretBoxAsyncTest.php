<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 11:41
 */

namespace Test;


use Phore\Core\Helper\PhoreSecretBoxAsync;
use PHPUnit\Framework\TestCase;

class PhoreSecretBoxAsyncTest extends TestCase
{


    public function testEncryptDecryptShortString()
    {
        $input = phore_random_str(45);
        
        $encryptor = new PhoreSecretBoxAsync();

        $keypair = json_decode($encryptor->createKeyPair());

        $encryptedText = $encryptor->encrypt($input, $keypair->public_key);
        $decryptedText = $encryptor->decrypt($encryptedText, $keypair->public_key, $keypair->private_key);
        
        $this->assertEquals($input, $decryptedText);
    }


    public function testEncryptDecryptLongString()
    {
        $input = phore_random_str(600);
        
        $encryptor = new PhoreSecretBoxAsync();

        $keypair = json_decode($encryptor->createKeyPair());

        $encryptedText = $encryptor->encrypt($input, $keypair->public_key);
        $decryptedText = $encryptor->decrypt($encryptedText, $keypair->public_key, $keypair->private_key);
        
        $this->assertEquals($input, $decryptedText);
    }
}
