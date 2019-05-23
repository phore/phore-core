<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 11:16
 */

namespace Test;



use PHPUnit\Framework\TestCase;

class PluckTest extends TestCase
{

    private $in = ["a" => ["b" => "res"]];

    public function testPluckWithArrayParameters()
    {
        $this->assertEquals("res", phore_pluck(["a", "b"], $this->in));
    }

    public function testPluckWithString()
    {
        $this->assertEquals("res", phore_pluck("a.b", $this->in));
    }

    public function testPluckWithDefault()
    {
        $this->assertEquals("def", phore_pluck("a.b.c", $this->in, "def"));
    }

    public function testPluckThowsException()
    {
        $this->expectException(\Exception::class);
        phore_pluck("a.b.c", $this->in, new \Exception("Ex"));
    }

    public function testPluckArrayData()
    {
        $input = [
            "a" => [
                ["b"=>"val1"],
                ["b"=>"val2"]
            ]
        ];
        $this->assertEquals(["val1", "val2"], phore_pluck("a[].b", $input));
    }

     public function testPluckArrayDataWithEmptyArray()
     {
        $input = [
            "a" => [
            ]
        ];
        // Must return empty array because the path is still valid
        $this->assertEquals([], phore_pluck("a[].b", $input));
     }

}
