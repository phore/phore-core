<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 06.05.19
 * Time: 13:27
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreRandomStrTest extends TestCase
{


    public function testRandomStrMatchesSize()
    {
        $ret = phore_random_str(120);
      //  echo $ret;
        $this->assertEquals(120, strlen($ret));
    }


    public function testRandomStrAreNotTheSame()
    {
        $str1 = phore_random_str(20);
        $str2 = phore_random_str(20);

        $this->assertNotEquals($str1, $str2);
     //   echo $str1;

    }

}
