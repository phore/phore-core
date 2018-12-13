<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 13.12.18
 * Time: 13:26
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class UnindentTest extends TestCase
{

    public function testUnindentWithNewline()
    {
        $input = <<<EOT

    Some indented
    Text
        With subtext

EOT;

        $expected = <<<EOT
Some indented
Text
    With subtext
EOT;
        $this->assertEquals($expected, phore_text_unindent($input));

    }

    public function testUnindentWithNoIndentaionNewline()
    {
        $input = <<<EOT

Some indented
Text
    With subtext

EOT;

        $expected = <<<EOT
Some indented
Text
    With subtext
EOT;
        $this->assertEquals($expected, phore_text_unindent($input));

    }

}
