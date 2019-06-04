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


    public function testEncryptDecryptShortString()
    {
        $secret = phore_random_str(45);

        $encryptor = new PhoreSecretBoxSync($secret);

        $input = phore_random_str(46);

        $this->assertEquals($input, $encryptor->decrypt($encryptor->encrypt($input)));
    }


    public function testEncryptDecryptLongString()
    {
        $secret = phore_random_str(512);

        $encryptor = new PhoreSecretBoxSync($secret);

        $input = phore_random_str(600);

        $this->assertEquals($input, $encryptor->decrypt($encryptor->encrypt($input)));
    }


    public function testCanDecrypt ()
    {

        $mockIn = "E1-eGuWfxufP1NPt50SHtAfQX9pPZL0933Pipbl95mw+sNW83z7/SeKHEfxWzvFm3J4EMnqvN9yqdpz35wb4FfcsmGl3xPysYWQjP8o6OGcxaLbF4df1ALMRoea5agdpU6QYr0R/ZfsiH8Tlptlz71cbVM3AkW0QTvVHLuSXm5WnM1F9TExOyTy51nuDqUKGGk2MKsYQZeN2qv4fSYJNC2amydDA8I8E5bxn8C3tuf/LsA/QHoKAuPN9E6EMXdlzFsxCy7w0Uenr3eCwmVT4BS3ncSL0V8MadOX6qyEbUrdR72t/hfikWHZz4rm5YL77QNl1zJmm5Vzv7oaQLsvWSF4TDwp2PN0TMrc8t9JfXAjEfHCgZYiXChewe9ngtSCohJjbk206hxphiK9j5nzuq4dofq+G0QeAmWzEqqOUh6bBxCaSuLxGyrixfKs+UuLjLyBzS0cFghWQRUJyE7L11VA3f3AgwOJqR+1Rzs5mxv/Jqrwpxjv2dGDORCQOuW4qwVwXSivEVqxkxKFtNcTLFka2INv1dAo396rqaW0PBJ74lJ4vP9UdR5XMbRxTUTFizaX+9s/2wFi0J2ZL9YPiCSnIzwBUGyuEiugTBQIWpTX6DMFkgpZ0bRHfb1x/4DmDX2OimymjU3bp4vhJBl08Ee8CDRT8i1eJNXTHhZOG4Nd2GZ5aANMjhuft+zkcD5rpm9bGriMjGxi+DRfcm39YIenvYDYIzQzTQqA";

        $encryptor = new PhoreSecretBoxSync("SECRET_KEY");

        $this->assertEquals("SECRET", $encryptor->decrypt($mockIn));
    }



}
