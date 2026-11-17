<?php

namespace spec\Phore\Core\Format;

use PhpSpec\ObjectBehavior;

class PhoreFormatSpec extends ObjectBehavior
{
    function it_formats_numeric_date_intervals()
    {
        $this->dateInterval(6001)->shouldBe("1h 40min 1sec");
    }
}
