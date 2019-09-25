<?php


namespace Phore\Core\Helper;

/**
 * Class PhoreGetOptResult
 * @package Phore\Core\Helper
 * @internal
 */
class PhoreGetOptResult
{
    private $result;
    private $optind;

    public function __construct(array $result, $optind)
    {
        $this->result = $result;
        $this->optind = $optind;
    }

    public function has(string $key)
    {
        return isset ($this->result[$key]);
    }


    public function get(string $key, $default=null) : string
    {
        if ( ! $this->has($key)) {
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        return $this->result[$key];

    }

    public function getArr(string $key, $default=null) : array
    {
        if ( ! $this->has($key)) {
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        $result = $this->result[$key];
        if ( ! is_array($result))
            return [ $result ];
        return $result;
    }

    public function argv()
    {

    }
}
