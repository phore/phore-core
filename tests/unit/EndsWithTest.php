<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 12:14
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class EndsWithTest extends TestCase
{


    public function testEndsWith()
    {
        $this->assertEquals(true, endsWith("abcdef", "def"));
        $this->assertEquals(false, endsWith("abcdef", "cde"));
    }

}
