<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 08.04.19
 * Time: 10:10
 */

namespace Phore\Core\Format;


use http\Exception\InvalidArgumentException;

class PhoreInput
{
    private $format = ["Y-m-d\TH:i:sP","Y-m-d\TH:i:s","Y-m-d\TH:i:s.uP","Y-m-d\TH:i:s.u","Y-m-d"];

    private function validateDate($date, $format)
    {
        foreach ($format as $form){
            $d = \DateTime::createFromFormat($form, $date);
            if($d && $d->format($form) == $date){
                return true;
            }
        }
        return false;
    }

    public function toTimestampUtc($input) : float
    {
        if(is_numeric($input) && strtotime(date('Y-m-d H:i:s',$input)) === (int)$input) {
            return $input;
        } else {
            if(!$this->validateDate($input, $this->format)){
                throw new InvalidArgumentException("Wrong Data Format");
            }
            $date = new \DateTime($input);
            return $date->format('U.u');
        }
    }
}
