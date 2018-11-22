<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 11:46
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{

    public function testDateIntervalNumericValue()
    {
        $this->assertEquals("1h 40min 1sec", phore_format()->dateInterval(6001));
    }


}
