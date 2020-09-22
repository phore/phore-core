<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 05.09.18
 * Time: 11:52
 */

use Phore\Core\Helper\PhoreGetOptResult;


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

    $asArray = false;
    if (is_array($default)) {
        $asArray = true;
    }

    $curKey = array_shift($key);
    if (endsWith($curKey, "[]")) {
        $curKey = substr($curKey, 0, -2);
        $asArray = true;
    }

    if (!is_array($data) || !array_key_exists($curKey, $data)) {
        if ($default instanceof Exception)
            throw $default;
        return $default;
    }

    $curData =& $data[$curKey];

    if ($asArray) {
        if ( ! is_array($curData)) {
            if ($default instanceof Exception)
                throw $default;
            return $default;
        }
        $ret = [];
        foreach ($curData as $index => &$curArrData) {
            $ret[$index] = phore_pluck($key, $curArrData, $default);
        }
        return $ret;
    }


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

/**
 * URL save base64 encoding (e.g. for JWT)
 *
 * @param string $data
 * @return string
 */
function phore_base64url_encode(string $data) : string
{
    $b64 = base64_encode($data);
    if ($b64 === false)
        throw new InvalidArgumentException("Cannot base64 encode data");
    return rtrim (strtr($b64, "+/", "-_"), "=");
}


/**
 * URL save base64 decoding (e.g. JWT)
 *
 * @param string $data
 * @param bool $strict
 * @return string|null
 */
function phore_base64url_decode(string $data, bool $strict=false) : ?string
{
    $data = strtr($data, "-_", "+/");
    $result = base64_decode($data, $strict);
    if ($result === false)
        return null;
    return $result;
}


function phore_format() : \Phore\Core\Format\PhoreFormat
{
    return new \Phore\Core\Format\PhoreFormat();
}


function phore_assert($value) : \Phore\Core\Helper\_PhoreAssert
{
    return new \Phore\Core\Helper\_PhoreAssert($value);
}


function phore_hash($input, bool $secure=false, bool $raw=false) : string
{
    if ( ! is_string($input))
        throw new InvalidArgumentException("Parameter 1 must be string");
    if (strlen($input) == 0)
        throw new InvalidArgumentException("Parameter 1 strlen is 0 (empty string)");
    if ($secure === false) {
        $hash = sha1($input, true);
        if ($raw) {
            return $hash;
        }
        return base64_encode($hash);
    }
    $hash = sha1(sha1($input, true) . "P", true) . md5(sha1($input, true) . "X", true);
    if ($raw) {
        return $hash;
    }

    return base64_encode($hash);
}


/**
 * Transform the input array into another array using the callback function
 * applied on each element of $input
 *
 * <pre>
 * $tbl = phore_array_transform($input, function ($key, $value) {
 *     return [
 *          "value" => $value,
 *          "key" => $key
 *     ]
 * });
 * </pre>
 *
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
function phore_escape (string $cmd, array $args, callable $escaperFn, bool $softFail=false) : string
{
    $argsCounter = 0;
    $cmd = preg_replace_callback( '/\?|\:[a-z0-9_\-]+|\{[a-z0-9_\-]+\}/i',
        function ($match) use (&$argsCounter, &$args, $escaperFn, $softFail) {
            if ($match[0] === '?') {
                if(! isset($args[0])){
                    if ($softFail)
                        return "?";
                    throw new \Exception("Index $argsCounter missing");
                }
                $argsCounter++;
                return $escaperFn(array_shift($args));
            }
            if ($match[0][0] === "{") {
                $key = substr($match[0], 1, -1);
            } else {
                $key = substr($match[0], 1);
            }
            if (!isset($args[$key])){
                if ($softFail)
                    return $match[0];
                throw new \Exception("Key '$key' not found");
            }
            return $escaperFn($args[$key], $key);
        },
        $cmd);
    return $cmd;
}

/*
 * Output a message to the defined channel including timing information
 *
 * @param $msg
 * @param bool $return
 */
function phore_out($msg=null, $return = false) {
    static $lastTime = null;
    static $firstTime = null;
    if ($lastTime === null) {
        $lastTime = $firstTime = microtime(true);
    }

    if (is_array($msg)) {
        $msg = implode(" ", $msg);
    }

    $str = "\n[" . number_format((microtime(true) - $firstTime), 3, ".", "") . "+" . number_format((microtime(true) - $lastTime), 3, ".", "") . "s] $msg";
    $lastTime = microtime(true);
    if ($return === true)
        return $str;
    echo $str;
}


/**
 * Check that input is a valid string and that it
 * has no special chars inside. Throws exception if
 * not.
 *
 * @param $input
 * @throws \Phore\Core\Exception\InvalidDataException
 * @throws InvalidArgumentException
 * @param array $allowedChars
 * @param Exception Exception to throw
 */
function phore_assert_str_alnum($input, array $allowedChars=[], Exception $throwException=null) : bool
{
    if ( ! is_string($input))
        throw new InvalidArgumentException("Parameter 1 is not type string.");
    $input = str_replace($allowedChars, '', $input); // Allow chars in parameter 2
    if ( ! ctype_alnum($input)) {
        if ($throwException === null)
            throw new \Phore\Core\Exception\InvalidDataException("Parameter 1 is not alphanumeric. [a-zA-Z0-9" . implode("", $allowedChars) . "]");
        throw $throwException;
    }
    return true;
}



/**
 * Print json nicely
 *
 * @param $json
 * @return string
 */
function phore_json_pretty_print(string $json, string $indent="\t") : string
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                $level--;
                $ends_line_level = NULL;
                $new_line_level = $level;
                break;

                case '{': case '[':
                $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                $char = "";
                $ends_line_level = $new_line_level;
                $new_line_level = NULL;
                break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( $indent, $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}



function phore_serialize($input) : string
{
    return serialize($input);
}

/**
 * Safe unserialize a string serialized with phore_serialize()
 *
 * The second parameter defines, how serialize deals with classes:
 * - false: (default): Don't allow object deserialisation at all
 * - true: Allow any object (dangerous! see https://www.php.net/manual/de/function.unserialize.php )
 * - [class1::class, class2::class]: Array of allowed class-names
 *
 * @param string $input
 * @param bool|string[] $allowedClasses
 * @return mixed
 */
function phore_unserialize(string $input, $allowedClasses=false)
{
    return unserialize($input, ["allowed_classes" => $allowedClasses]);
}

function phore_json_encode($input) : string
{
    $ret = json_encode($input, JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    if ($ret === false)
        throw new InvalidArgumentException("Cannot json_encode() input data: " . json_last_error_msg());
    return $ret;
}

/**
 * @param string $input
 * @return array
 * @throws InvalidArgumentException
 */
function phore_json_decode(string $input) : array
{
    $ret = json_decode($input, true, 512, JSON_PRESERVE_ZERO_FRACTION);
    if ($ret === null)
        throw new InvalidArgumentException("Cannot json_decode() input data: " . json_last_error_msg());
    if ( ! is_array($ret))
        throw new InvalidArgumentException("phore_json_decode(): Simple data import (string, int, bool) not supported.");
    return $ret;
}

/**
 * @param $input
 * @return string
 */
function phore_yaml_encode($input) : string
{
    if ( ! function_exists("yaml_parse"))
        throw new InvalidArgumentException("yaml-ext is missing. please install php yaml extension.");
    return yaml_emit($input);
}

/**
 *
 *
 * @param string $input
 * @throws InvalidArgumentException
 * @return array
 */
function phore_yaml_decode(string $input) : array
{
    if ( ! function_exists("yaml_parse"))
        throw new InvalidArgumentException("yaml-ext is missing. please install php yaml extension.");

    try {
        $ret = yaml_parse($input);
    } catch (ErrorException $e) {
        throw new InvalidArgumentException(
            "phore_yaml_decode(): {$e->getMessage()}",
            0,
            $e
        );
    }

    if ($ret === false) {
        $err = error_get_last();
        throw new InvalidArgumentException(
            "Invalid yaml input: {$err["message"]}",
            0
        );
    }
    return $ret;
}


/**
 * Return cryptographic safe string of alphanumeric chars
 *
 * Will use libsodium in parallel to php buildIn random_int() function
 *
 * @param int $len
 * @return string
 */
function phore_random_str (int $len = 12, $requireSodium=false) : string
{
    $keysAvail = "abcdefghijklmopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $keylen = strlen($keysAvail)-1;

    $fns = [
        function() use ($keylen, $keysAvail) {
            return $keysAvail[random_int(0, $keylen)];
        }
    ];

    // If libsodium is installed: use it too.
    if (function_exists("\\Sodium\\randombytes_uniform")) {
        $fns[] = function () use ($keylen, $keysAvail) {
            return $keysAvail[Sodium\randombytes_uniform($keylen)];
        };
    } else {
        if ($requireSodium === true)
            throw new InvalidArgumentException("Libsodium is required for phore_random_str()");
    }

    $key = "";
    for ($i = 0; $i<$len; $i++) {
        $key .= $fns[$data = mt_rand(0, count ($fns) - 1)]();
    }
    if (strlen($key) !== $len)
        throw new InvalidArgumentException("phore_random_str(): produced invalid output len.");

    return $key;
}

/**
 *
 * Returns:
 *      string      with value of the annotion
 *      array       with elements separated by space count $arrayParts
 *      null        if the annotation was not found
 *
 * @param string $text
 * @param string $annotationName
 * @param int|null $arrayParts
 * @return array|null|string
 */
function phore_parse_annotation(string $text, string $annotationName, int $arrayParts = null)
{
    if (substr($annotationName, 0,1) !== "@")
        throw new InvalidArgumentException("Parameter 2 must start with '@' character");

    $startPos = strpos($text, $annotationName);
    if ($startPos === false)
        return null;

    $val = substr($text, $startPos + strlen($annotationName));

    $val = substr($val, 0, strpos($val, "\n"));
    $val = trim ($val);

    if ($arrayParts === null)
        return $val;

    $params = explode(" ", $val, $arrayParts);
    for ($i=count($params); $i<$arrayParts; $i++)
        $params[] = null;
    return $params;
}


/**
 * Parse command line options
 *
 * Wrapper around php function 'getopt()'
 *
 * <pre>
 * $opts = phore_getopt("hf:", ["file:"]);
 *
 * // cmd-call: script.php -h -f abcd --file file1 --file file2
 *
 * assert( true === $opts->has("h") );
 * assert( "abcd" === $opts->get("f") );
 * assert( ["file1", "file2"] === $opts->getArr("file") );
 * assert( "default" === $opts->get("missing", "default") );
 * </pre>
 *
 * @see https://github.com/phore/phore-core/blob/master/doc/example/phore_getopt.php
 * @see https://www.php.net/manual/en/function.getopt.php
 * @param string $options
 * @param array $longopts
 * @param int|null $optind
 * @return PhoreGetOptResult
 */
function phore_getopt(string $options, array $longopts = [], int &$optind = null) : PhoreGetOptResult
{
    $opt = getopt($options, $longopts, $optind);
    return new PhoreGetOptResult($opt, $optind);
}


/**
 * Parse a url and return PhoreUrl object
 *
 * @param string $url
 * @param string|null $default
 * @return \Phore\Core\Helper\PhoreUrl
 */
function phore_parse_url(string $url, string $default=null) : \Phore\Core\Helper\PhoreUrl
{
    $schema = parse_url($url);
    if ($schema === false)
        throw new InvalidArgumentException("Cannot parse url in parameter 1: '$url'");

    $preset = [
        "scheme" => null, "host" => null, "port" => null, "user" => null, "pass" => null,
        "path" => null, "query" => null, "fragment" => null
    ];

    if ($default !== null) {
        $presetDefaults = parse_url($default);
        if ($presetDefaults === false)
            throw new InvalidArgumentException("Cannot parse default url in parameter 2: '$default'");
        $preset = array_merge($preset, $presetDefaults);
    }
    $schema = array_merge($preset, $schema);

    $retUrlObj = new \Phore\Core\Helper\PhoreUrl();
    foreach ($schema as $key => $val)
        $retUrlObj->$key = $val;
    return $retUrlObj;
}


/**
 * Run callable only once per script run
 *
 * Return the result of previous calls
 *
 * If parameter 2 is specified, add the key to the position
 * in code
 *
 * @param callable $callOnce
 * @param string $key
 * @return mixed
 */
function phore_once(callable $callOnce, string $key="")
{
    static $runs = [];

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $key = $backtrace[0]["file"] . $backtrace[0]["line"] . $key;
    if (array_key_exists($key, $runs))
        return $runs[$key];
    $runs[$key] = $ret = $callOnce();
    return $ret;
}


