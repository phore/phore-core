<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.09.19
 * Time: 13:20
 */

namespace Test;


use PHPUnit\Framework\TestCase;

/**
 * Class TestObj
 * @package Test
 * @internal
 */
class TestObj {

}

class PhoreSerializeTest extends TestCase
{

    public function testObjectDeSerializerWithAnyClass()
    {
        $obj = new TestObj();
        $serialized_data = phore_serialize($obj);

        // Dangerous mode: Allow all classes
        $unserialized = phore_unserialize($serialized_data, true);

        self::assertEquals(TestObj::class, get_class($unserialized));
    }

    public function testObjectDeserialisationDefaultOff()
    {
        $obj = new TestObj();
        $serialized_data = phore_serialize($obj);

        // Default mode: Don't allow objects at all (SECURITY BY DEFAULT)
        // Read: https://www.php.net/manual/de/function.unserialize.php
        $unserialized = phore_unserialize($serialized_data);

        self::assertEquals(\__PHP_Incomplete_Class::class, get_class($unserialized));
    }


    public function testObjectDeserialisationWithExplicitClasses()
    {
        $obj = new TestObj();
        $serialized_data = phore_serialize($obj);

        // Default mode: Don't allow objects at all (SECURITY BY DEFAULT)
        // Read: https://www.php.net/manual/de/function.unserialize.php
        $unserialized = phore_unserialize($serialized_data, [TestObj::class]);

        self::assertEquals(TestObj::class, get_class($unserialized));
    }


}
