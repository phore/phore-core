<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.05.19
 * Time: 13:45
 */

namespace Phore\Core\Helper;


use Phore\Core\Exception\InvalidDataException;

class _PhoreAssert
{

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param int $filter
     * @param null $options
     * @param \Exception|null $throw
     * @return _PhoreFilter
     * @throws InvalidDataException
     */
    public function filter(int $filter=FILTER_DEFAULT, $options=null, \Exception $throw = null) : self
    {
        $ret = filter_var($this->value, $filter, $options);
        if ($ret === false) {
            if ($throw !== null)
                throw $throw;
            throw new InvalidDataException("Invalid input data. (Filter: $filter)");
        }
        return $this;
    }

    /**
     * Verify value is a valid e-mail address
     * 
     * @param \Exception|null $throwException
     * @throws InvalidDataException
     */
    public function email(\Exception $throwException=null) : self
    {
        if ($throwException === null)
            $throwException = new InvalidDataException("Input '$this->value' is not a valid email address.");
        $this->filter(FILTER_VALIDATE_EMAIL, null, $throwException);
        return $this;
    }

}
