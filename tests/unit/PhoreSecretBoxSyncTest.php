<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 11:41
 */

namespace Test;


use Phore\Core\Helper\PhoreSecretBoxSync;
use PHPUnit\Framework\TestCase;

class PhoreSecretBoxSyncTest extends TestCase
{


    public function testEncryptDecrypt()
    {
        $secret = phore_random_str(512);

        $encryptor = new PhoreSecretBoxSync($secret);

        $input = "ABC";

        $this->assertEquals($input, $encryptor->decrypt($encryptor->encrypt($input)));
    }

}
