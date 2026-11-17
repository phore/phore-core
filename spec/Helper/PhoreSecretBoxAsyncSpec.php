<?php

namespace spec\Phore\Core\Helper;

use PhpSpec\ObjectBehavior;

class PhoreSecretBoxAsyncSpec extends ObjectBehavior
{
    function it_encrypts_and_decrypts_short_strings()
    {
        $input = phore_random_str(45);
        $keys = $this->createKeyPair();
        $cipher = $this->encrypt($input, $keys["public_key"]);
        $this->decrypt($cipher, $keys["private_key"])->shouldBe($input);
    }

    function it_encrypts_and_decrypts_long_strings()
    {
        $input = phore_random_str(600);
        $keys = $this->createKeyPair();
        $cipher = $this->encrypt($input, $keys["public_key"]);
        $this->decrypt($cipher, $keys["private_key"])->shouldBe($input);
    }

    function it_decrypts_existing_ciphertext()
    {
        $cipher = "EA1-28dCQ34lZB1ZRjbSecvGV0yVR2yFiLzeU0Rihq0dRwKI7X98H10aOcJKrbMHPW76ByM2iOooSo6oi2R65Ab8DM0NigQLt8+AioTF5gqSiUdL20R0NFL/u5RCLPyKpop9YGj4ViUKQZEWmgFyczNExRJCng==";
        $this->decrypt($cipher, "795GkTRjTUCFUsw2nAGnD\/9QU0YvSFyQ\/ljCnW4+Zds=")
            ->shouldBe("SecretSecretSecret");
    }
}
