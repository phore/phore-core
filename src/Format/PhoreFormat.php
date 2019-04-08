<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 22.11.18
 * Time: 11:07
 */

namespace Phore\Core\Format;


class PhoreFormat
{


    public static $config = [
        "number" => [
            "dec_point" => ".",
            "thousands_sep" => ""
        ]
    ];


    /**
     * @param $dateInterval \DateInterval|int
     * @return string
     */
    public function dateInterval($dateInterval)
    {
        $rest = $dateInterval;

        $r = "";
        $out = false;
        $y = (int)($rest / 3.154e+7);
        if ($y > 0) {
            $r .= $y . " years ";
            $rest -= $y * 3.154e+7;
            $out = true;
        }

        $m = (int)($rest / 2.628e+6);
        if ($m > 0 || $out) {
            $r .= $m . " months ";
            $rest -= $m * 2.628e+6;
            $out = true;
        }
        $d = (int)($rest / 86400);
        if ($d > 0 || $out) {
            $r .= $d . " days ";
            $rest -= $d * 86400;
            $out = true;
        }

        $h = (int)($rest / 3600);
        if ($h > 0 || $out) {
            $r .= $h . "h ";
            $rest -= $h * 3600;
            $out = true;
        }

        $i = (int)($rest / 60);
        if ($i > 0 || $out) {
            $r .= $i . "min ";
            $rest -= $i * 60;
            $out = true;
        }
        $r .= $rest . "sec";
        return $r;
    }

    public function number($number, $decimals=2) : string
    {
        return number_format($number, $decimals, self::$config["number"]["dec_point"], self::$config["number"]["thousands_sep"]);
    }

    public function filesize($size) : string
    {
        if ($size > 1000000) {
            return $this->number($size / 1000000) . "MB";
        }
        if ($size > 1000) {
            return $this->number($size / 1000) . "kB";
        }
        return $this->number($size / 1000) . "B";
    }



    
}
