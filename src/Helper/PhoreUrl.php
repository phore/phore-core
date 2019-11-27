<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.19
 * Time: 17:34
 */

namespace Phore\Core\Helper;


class PhoreUrl
{
    public $scheme;

    public $host;

    public $port;

    public $user;

    public $pass;

    public $path;

    public $query;

    public $fragment;


    /**
     * Return the parsed query string
     *
     * If parameter 1 is set, returns the content of this index in query stirng
     * or default if not available.
     *
     * @param string|null $key
     * @param null $default
     * @return null
     * @throws \Exception
     */
    public function getQueryVal(string $key = null, $default=null)
    {
        parse_str($this->query, $result);
        if ($key === null)
            return $result;
        return phore_pluck($key, $result, $default);
    }

}
