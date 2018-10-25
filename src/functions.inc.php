<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 05.09.18
 * Time: 11:52
 */


/**
 * Pluck elements from arrays
 *
 * @param $key
 * @param $data
 * @param null $default
 * @return null
 * @throws Exception
 */
function phore_pluck ($key, &$data, $default=null)
{
    if (is_string($key) && strpos($key, ".") !== false) {
        $key = explode(".", $key);
    }

    if ( ! is_array($key))
        $key = [$key];

    if (count($key) === 0)
        return $data;

    $curKey = array_shift($key);
    if ( ! isset ($data[$curKey])) {
        if ($default instanceof Exception)
            throw $default;
        return $default;
    }
    $curData =& $data[$curKey];
    return phore_pluck($key,$curData, $default);
}
