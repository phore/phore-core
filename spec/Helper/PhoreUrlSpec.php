<?php

namespace spec\Phore\Core\Helper;

use PhpSpec\ObjectBehavior;
use spec\Phore\Core\fixtures\Functions;

class PhoreUrlSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["http://user:pass@host:80/path?query#fragment"]);
    }

    function it_parses_url_components()
    {
        $this->scheme->shouldBe("http");
        $this->host->shouldBe("host");
        $this->port->shouldBe(80);
        $this->path->shouldBe("/path");
        $this->user->shouldBe("user");
        $this->pass->shouldBe("pass");
        $this->query->shouldBe("query");
        $this->fragment->shouldBe("fragment");
    }

    function it_uses_default_url_components()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["https://host/path", "http://hostdef:80/pathdef"]);
        $this->scheme->shouldBe("https");
        $this->host->shouldBe("host");
        $this->port->shouldBe(80);
        $this->path->shouldBe("/path");
    }

    function it_reads_query_parameters_with_defaults()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["https://host/path", "http://hostdef:80?key=val"]);
        $this->getQueryVal()->shouldBe(["key" => "val"]);
        $this->getQueryVal("key")->shouldBe("val");
        $this->getQueryVal("nonKey", "def")->shouldBe("def");
    }

    function it_reads_absent_queries_without_deprecations()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["https://host/path", "http://hostdef:80"]);
        $this->getQueryVal()->shouldBe([]);
        $this->getQueryVal("nonKey", "def")->shouldBe("def");
        $this->hasQueryVal("key")->shouldBe(false);
    }

    function it_preserves_absolute_file_paths()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["file:///path/to/file.txt"]);
        $this->path->shouldBe("/path/to/file.txt");
    }

    function it_builds_urls()
    {
        $url = "http://user:pass@server.name/path/to?someParam&p1=val1#fragment";
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], [$url]);
        $this->getAsString()->shouldBe($url);
    }

    function it_overwrites_query_parameters()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["http://server.name?someParam&p1=val1"]);
        $this->withQueryParam("p1", "newVal")->getAsString()
            ->shouldBe("http://server.name?someParam=&p1=newVal");
    }

    function it_removes_query_parameters()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["http://server.name?someParam&p1=val1"]);
        $this->withQueryParam("p1", null)->getAsString()->shouldBe("http://server.name?someParam=");
        $this->withQueryParams()->getAsString()->shouldBe("http://server.name");
    }

    function it_adds_parameters_to_absent_queries_without_deprecations()
    {
        $this->beConstructedThrough([new Functions(), 'phore_parse_url'], ["https://host/path"]);
        $this->withQueryParam("key", "val")->getAsString()->shouldBe("https://host/path?key=val");
        $this->withQueryParams(["key" => "val"])->getAsString()->shouldBe("https://host/path?key=val");
        $this->getQueryVal()->shouldBe([]);
    }
}
