<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 29.03.19
 * Time: 15:16
 */

namespace Test;


use Phore\Core\Exception\PhoreException;
use PHPUnit\Framework\TestCase;

class PhoreJsonTest extends TestCase
{

    public function testPreservesNumberFractions()
    {
        $in = [(float)12];
        $out = phore_json_decode(phore_json_encode($in));
        $this->assertEquals($in, $out);
    }

    public function testDecodeWillThrowExceptionOnSimpleDataType()
    {
        $in = 123;
        $this->expectException(\InvalidArgumentException::class);
        $out = phore_json_decode(phore_json_encode($in));

    }


    public function testEscaping()
    {
        $escaped = phore_json_encode(["data" => "/"]);
        $this->assertEquals('{"data":"/"}', $escaped); // Don't escape slashes

        $escaped = phore_json_decode($escaped);
        $this->assertEquals(["data"=>"/"], $escaped);
    }
}
