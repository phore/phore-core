<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 13:26
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class PhoreParseAnnotationTest extends TestCase
{

    const FIXTURE = <<<EOT
    /**
     * Some text
     *
     * @an0
     * @an1 abc
     * @an2 abc def
     * @an3 abc def ghi
     */
EOT;


    public function testAnnotationParse()
    {
        $this->assertEquals(null, phore_parse_annotation(self::FIXTURE, "@notexistent"));
        $this->assertEquals("", phore_parse_annotation(self::FIXTURE, "@an0"));

        $this->assertEquals("abc", phore_parse_annotation(self::FIXTURE, "@an1"));
        $this->assertEquals("abc def", phore_parse_annotation(self::FIXTURE, "@an2"));

        $this->assertEquals(["abc"], phore_parse_annotation(self::FIXTURE, "@an1", 1));
        $this->assertEquals(["abc def"], phore_parse_annotation(self::FIXTURE, "@an2", 1));

        $this->assertEquals(["abc", null], phore_parse_annotation(self::FIXTURE, "@an1", 2));
        $this->assertEquals(["abc", "def"], phore_parse_annotation(self::FIXTURE, "@an2", 2));
        $this->assertEquals(["abc", "def ghi"], phore_parse_annotation(self::FIXTURE, "@an3", 2));
    }

}
