<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.19
 * Time: 17:45
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class ParseUrlTest extends TestCase
{


    public function testParseUrl()
    {
        $url = phore_parse_url("http://user:pass@host:80/path?query#fragment");

        $this->assertEquals("http", $url->scheme);
        $this->assertEquals("host", $url->host);
        $this->assertEquals("80", $url->port);
        $this->assertEquals("/path", $url->path);
        $this->assertEquals("user", $url->user);
        $this->assertEquals("pass", $url->pass);
        $this->assertEquals("query", $url->query);
        $this->assertEquals("fragment", $url->fragment);
    }


    public function testDefaultUrl()
    {
        $url = phore_parse_url("https://host/path", "http://hostdef:80/pathdef");

        $this->assertEquals("https", $url->scheme);
        $this->assertEquals("host", $url->host);
        $this->assertEquals("80", $url->port);
        $this->assertEquals("/path", $url->path);
    }

}
