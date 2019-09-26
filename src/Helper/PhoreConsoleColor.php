<?php


namespace Phore\Core\Helper;

/**
 * Class PhoreConsoleColor
 *
 * <pre>
 * PhoreConsoleColor::SetFg("red");
 * echo "hello";
 * PhoreConsoleColor::Default();
 * </pre>
 *
 * @package Phore\Core\Helper
 */
class PhoreConsoleColor
{
    const FG = [
        'black'         => '0;30',
        'dark_gray'     => '1;30',
        'blue'          => '0;34',
        'light_blue'    => '1;34',
        'green'         => '0;32',
        'light_green'   => '1;32',
        'cyan'          => '0;36',
        'light_cyan'    => '1;36',
        'red'           => '0;31',
        'light_red'     => '1;31',
        'purple'        => '0;35',
        'light_purple'  => '1;35',
        'brown'         => '0;33',
        'yellow'        => '1;33',
        'light_gray'    => '0;37',
        'white'         => '1;37'
    ];
    const BG = [
        'black'     => '40',
        'red'       => '41',
        'green'     => '42',
        'yellow'    => '43',
        'blue'      => '44',
        'magenta'   => '45',
        'cyan'      => '46',
        'light_gray'=> '47'
    ];


    public static function SetColor(string $fg_color=null, string $bg_color=null)
    {
        $fg = $fg_color !== null ? self::FG[$fg_color] : "";
        $bg = $bg_color !== null ? self::BG[$bg_color] : "";
        echo "\033[" . $fg . "m";
        echo "\033[" . $bg . "m";
    }

    public static function Default()
    {
        echo "\033[0m";
    }
}
