<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 08.05.19
 * Time: 10:31
 */

namespace Phore\Core\Exception;

/**
 * Class InvalidInputException
 *
 * Exception thrown if user input was invalid.
 *
 * @package Phore\Core\Exception
 */
class InvalidDataException extends PhoreException
{
    protected $exceptions = [];

    /**
     * Data Exceptions might be triggered by multiple Exceptions.
     * 
     * @param InvalidDataException $invalidDataException
     */
    public function addException(InvalidDataException $invalidDataException)
    {
        $this->exceptions[] = $invalidDataException;
    }

    /**
     * 
     * 
     * @return InvalidDataException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

}
