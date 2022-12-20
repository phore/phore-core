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
     * Validate the value is safe for url injection.
     *
     * @return string|null
     * @throws InvalidDataException
     */
    public function safeString(array $allowChars = [], \Exception $exception = null)
    {
        if ( ! phore_assert_str_alnum($this->value, $allowChars, $exception)) {
            return null;
        }
        return $this->value;
    }

    /**
     * Validate the value is safe for url injection.
     *
     * @return string|null
     * @throws InvalidDataException
     */
    public function safeFileNameComponentString(bool $allowSlash = false, \Exception $exception = null)
    {
        if (str_contains($this->value, "..")) {
            if ($exception !== null)
                throw $exception;
            throw new InvalidDataException("String is not a save FileNameComponenentString: '$this->value'" );
        }
        $allowChars = ["."];
        if ($allowSlash)
            $allowChars[] = "/";
        $this->safeString($allowChars, $exception);
        return $this->value;
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
