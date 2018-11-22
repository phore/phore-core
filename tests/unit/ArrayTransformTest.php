<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 12:17
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class ArrayTransformTest extends TestCase
{

    public function testArrayTransform()
    {
        $in = ["a", "b"];

        $out = phore_array_transform($in, function ($key, $value) {
            return "$key-$value";
        });

        $this->assertEquals(["0-a", "1-b"], $out);

    }

}
