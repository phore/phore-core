<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.05.19
 * Time: 14:05
 */

namespace Phore\Core\Helper;


class PhoreObjectManipulator
{

    private $object;
    
    public function __construct($object)
    {
        if ( ! is_object($object))
            throw new \InvalidArgumentException("Parameter 1 is required to be object.");
        $this->object = $object;
    }

    public function setProperties(array $values)
    {
        $ref = new \ReflectionObject($this->object);
        foreach ($ref->getProperties() as $property) {
            if (isset ($values[$property->name])) {
                $property->setAccessible(true);
                $property->setValue($this->object, $values[$property->name]);
            }
        }
    }

    public function getProperties() : array
    {
        $ret = [];
        $ref = new \ReflectionObject($this->object);
        foreach ($ref->getProperties() as $property) {
            $property->setAccessible(true);
            $ret[$property->name] = $property->getValue($this->object);
        }
        return $ret;
    }
    
    
    
    public static function Get($object) : PhoreObjectManipulator
    {
        return new self($object);        
    }

}
