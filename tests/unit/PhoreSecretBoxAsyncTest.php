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

    public function testDecryption ()
    {

         $mockInput = "E1-28dCQ34lZB1ZRjbSecvGV0yVR2yFiLzeU0Rihq0dRwKI7X98H10aOcJKrbMHPW76ByM2iOooSo6oi2R65Ab8DM0NigQLt8+AioTF5gqSiUdL20R0NFL/u5RCLPyKpop9YGj4ViUKQZEWmgFyczNExRJCng==";

         $encryptor = new PhoreSecretBoxASync();

         $this->assertEquals("SecretSecretSecret", $encryptor->decrypt($mockInput, "N2WpcHSvMFHKVJFNFCpITqxvZfeGVoLASoQkWlWaEm0=", "795GkTRjTUCFUsw2nAGnD\/9QU0YvSFyQ\/ljCnW4+Zds="));
    }   
}
