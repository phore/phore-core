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
}
