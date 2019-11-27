<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.19
 * Time: 21:43
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreOnceTest extends TestCase
{


    public function testRunsOnlyOnce()
    {

        $count = 0;
        for($i=0; $i<3; $i++) {
            $ret = phore_once(function () use (&$count) {
                $count++;
            });
        }
        $this->assertEquals(1, $count);
        $this->assertEquals(null, $ret);
    }


    public function testReturnsSameResult()
    {

        for($i=0; $i<3; $i++) {
            $ret = phore_once(function () {
                static $count = 0;
                $count++;
                return $count;
            });
            $this->assertEquals(1, $ret);
        }
    }
}
