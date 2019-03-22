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
    if (! is_array($data) || ! array_key_exists($curKey, $data)) {
        if ($default instanceof Exception)
            throw $default;
        return $default;
    }
    $curData =& $data[$curKey];
    return phore_pluck($key,$curData, $default);
}

function startsWith($haystack, $needle) : bool
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) : bool
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * Unindent (strip trailing whitespace) string
 *
 * @param string $text
 * @return string
 */
function phore_text_unindent(string $text) : string {
    if ( ! preg_match('/\n([ \t]*)\S+/', $text, $matches)) {
        return $text;
    }
    return trim(str_replace("\n" . $matches[1], "\n", $text));
}



function phore_format() : \Phore\Core\Format\PhoreFormat
{
    return new \Phore\Core\Format\PhoreFormat();
}


/**
 * Transform the input array into another array using the callback function
 * applied on each element of $input
 *
 * @param array $input
 * @param callable $callback
 * @return array
 */
function phore_array_transform (array $input, callable $callback) : array
{
    $out = [];
    foreach ($input as $key => $value) {
        $ret = $callback($key, $value);
        if ($ret === null)
            continue;
        $out[] = $ret;
    }
    return $out;
}


/**
 * Escape parameters joined into a string using a escaper function
 *
 * @param string $cmd
 * @param array $params
 * @param callable $escaperFn
 * @return string
 */
function phore_escape (string $cmd, array $args, callable $escaperFn) : string
{
    $argsCounter = 0;
    $cmd = preg_replace_callback( '/\?|\:[a-z0-9_\-]+|\{[a-z0-9_\-]+\}/i',
        function ($match) use (&$argsCounter, &$args, $escaperFn) {
            if ($match[0] === '?') {
                if(! isset($args[$argsCounter])){
                    throw new \Exception("Index $argsCounter missing");
                }
                $argsCounter++;
                return escapeshellarg(array_shift($args));
            }
            if ($match[0][0] === "{") {
                $key = substr($match[0], 1, -1);
            } else {
                $key = substr($match[0], 1);
            }
            if (!isset($args[$key])){
                throw new \Exception("Key '$key' not found");
            }
            return $escaperFn($args[$key], $key);
        },
        $cmd);
    return $cmd;
}

