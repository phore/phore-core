<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 12:14
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class StartsWithTest extends TestCase
{


    public function testStartsWith()
    {
        $this->assertEquals(true, startsWith("abcdef", "abc"));
        $this->assertEquals(false, startsWith("abcdef", "bcd"));
    }

}
