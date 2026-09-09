<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 11:39
 */

namespace Test;


use Phore\Core\Exception\YamlDecodeException;
use PHPUnit\Framework\TestCase;

class PhoreYamlTest extends TestCase
{


    public function testYamlEncodeDecode()
    {
        $input = ["a"=>"b"];

        $this->assertEquals($input, phore_yaml_decode(phore_yaml_encode($input)));
    }


    public function testYamlDecodeExceptionExposesErrorLocation()
    {
        $yaml = "valid: true\nnested:\n\tinvalid: value\n";

        try {
            phore_yaml_decode($yaml);
            $this->fail("Expected YamlDecodeException");
        } catch (YamlDecodeException $e) {
            $this->assertStringContainsString("column", $e->getMessage());
            $this->assertSame(3, $e->getErrorLine());
            $this->assertSame(1, $e->getErrorColumn());
            $this->assertSame("\tinvalid: value", $e->getErrorSourceLine());
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

}
