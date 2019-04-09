<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 08.04.19
 * Time: 10:12
 */

namespace Test;


use Phore\Core\Format\PhoreInput;
use PHPUnit\Framework\TestCase;

class PhoreInputTest extends TestCase
{
    public function testToTimestampUtc(){
        $phoreInput = new PhoreInput();
        //Timestamp with mircoseconds
        $this->assertEquals(1554717860.546789, $phoreInput->toTimestampUtc("1554717860.546789"));
        //Timestamp
        $this->assertEquals(1554717860, $phoreInput->toTimestampUtc("1554717860"));
        //Format = Y-m-d\TH:i:s
        $this->assertEquals(1554725060, $phoreInput->toTimestampUtc("2019-04-08T12:04:20"));
        //Format = Y-m-d\TH:i:s.u
        $this->assertEquals(1554725060.123456, $phoreInput->toTimestampUtc("2019-04-08T12:04:20.123456"));
        //Format = Y-m-d\TH:i:sP
        $this->assertEquals(1554717860, $phoreInput->toTimestampUtc("2019-04-08T12:04:20+02:00"));
        //Format = Y-m-d\TH:i:s.uP
        $this->assertEquals(1554717860.123456, $phoreInput->toTimestampUtc("2019-04-08T12:04:20.123456+02:00"));
        //Format = Y-m-d
        $this->assertEquals(1554681600.0, $phoreInput->toTimestampUtc("2019-04-08"));
        //Exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Wrong Date Format: 05-2019-03");
        $phoreInput->toTimestampUtc("05-2019-03");
    }
}
