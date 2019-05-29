<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 28.05.19
 * Time: 09:40
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreHashTest extends TestCase
{


    public function testPhoreHashNormal()
    {
        $this->assertEquals("qZk+NkcGgWq6PiVxeFDCbJzQ2J0=", phore_hash("abc"));

    }

    public function testPhoreHashSecure()
    {
        $this->assertEquals("9PXC3z2TWd4Lu7+e7Xza05RPH+5LXmT6NDCUx9FYdxuek4KP", phore_hash("abc", true));

    }

    public function testPhoreHashWithInvalidInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ret = phore_hash(null);
    }

    public function testPhoreHashWithZeroLengthString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ret = phore_hash("");
    }
}
