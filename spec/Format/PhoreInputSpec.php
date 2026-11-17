<?php

namespace spec\Phore\Core\Format;

use PhpSpec\ObjectBehavior;

class PhoreInputSpec extends ObjectBehavior
{
    function it_parses_utc_timestamps_and_dates()
    {
        $this->toTimestampUtc("1554717860.546789")->shouldBe(1554717860.546789);
        $this->toTimestampUtc("1554717860")->shouldBe(1554717860.0);
        $this->toTimestampUtc("2019-04-08T12:04:20")->shouldBe(1554725060.0);
        $this->toTimestampUtc("2019-04-08T12:04:20.123456")->shouldBe(1554725060.123456);
        $this->toTimestampUtc("2019-04-08T12:04:20+02:00")->shouldBe(1554717860.0);
        $this->toTimestampUtc("2019-04-08T12:04:20.123456+02:00")->shouldBe(1554717860.123456);
        $this->toTimestampUtc("2019-04-08")->shouldBe(1554681600.0);
    }

    function it_rejects_invalid_date_formats()
    {
        $this->shouldThrow(new \InvalidArgumentException("Wrong Date Format: 05-2019-03"))
            ->during('toTimestampUtc', ["05-2019-03"]);
    }
}
