<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 08.05.19
 * Time: 10:33
 */

namespace Test;


use Phore\Core\Exception\InvalidDataException;
use PHPUnit\Framework\TestCase;

class AssertStrAlnumTest extends TestCase
{


    public function testValidInput()
    {
        $this->assertEquals(true, phore_assert_str_alnum("abcde_-", ["_", "-"]));
    }

    public function testThrowsInvalidArgumentOnWrongDataType()
    {
        $this->expectException(\InvalidArgumentException::class);
        phore_assert_str_alnum(1);
    }


    public function testThrowsInvalidDataExceptionOnWrongInput()
    {
        $this->expectException(InvalidDataException::class);
        phore_assert_str_alnum("ab-");
    }

}
