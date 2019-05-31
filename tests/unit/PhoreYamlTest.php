<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 11:39
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreYamlTest extends TestCase
{


    public function testYamlEncodeDecode()
    {
        $input = ["a"=>"b"];

        $this->assertEquals($input, phore_yaml_decode(phore_yaml_encode($input)));
    }

}
