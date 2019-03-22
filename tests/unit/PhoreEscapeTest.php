<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 12:14
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreEscapeTest extends TestCase
{


    public function testAllPossibilities()
    {
        $this->assertEquals(
            "cmd 'A' 'B' 'C'",
            phore_escape("cmd ? :b {c}", ["A", "b"=>"B", "c"=>"C"],
                function($in) {return escapeshellarg($in);}
                )
        );

    }

}
