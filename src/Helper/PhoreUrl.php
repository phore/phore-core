<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.19
 * Time: 17:34
 */

namespace Phore\Core\Helper;


class PhoreUrl
{
    public $scheme;

    public $host;

    public $port;

    public $user;

    public $pass;

    public $path;

    public $query;

    public $fragment;


    /**
     * Return the parsed query string
     *
     * If parameter 1 is set, returns the content of this index in query stirng
     * or default if not available.
     *
     * @param string|null $key
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public function getQueryVal(string $key = null, $default=null)
    {
        parse_str($this->query, $result);
        if ($key === null)
            return $result;
        return phore_pluck($key, $result, $default);
    }

    /**
     * Check if a key exists in query
     *
     * @param string $key
     * @return bool
     */
    public function hasQueryVal(string $key) : bool
    {
        parse_str($this->query, $result);
        return isset ($result[$key]);
    }


    /**
     * Return new instance with host set to value in parameter 1
     *
     * Unsets the hostname if parameter is null
     *
     * @param string|null $host
     * @return $this
     */
    public function withHost(string $host = null) : self
    {
        $new = clone ($this);
        $new->host = $host;
        return $new;
    }


    /**
     * Set / Unset User /password
     * 
     * @param string|null $user
     * @param string|null $pass
     * @return PhoreUrl
     */
    public function withUserPass(string $user=null, string $pass = null) : self
    {
        $new = clone($this);
        $new->user = $user;
        $new->pass = $pass;
        return $new;
    }
    

    /**
     * Set/Add a new query parameter to the url and create
     * a new instance of this object
     *
     * <example>
     *  echo phore_parse_url("https://host/?p1=val1&p2=val2")->withQueryParam("p2", "newVal");
     *
     *  => https://host/?p1=val1&p2=newVal
     * </example>
     *
     * If parameter 2 is NULL, the parameter will be unset.
     *
     * @param string $key
     * @param string|null $value
     * @return $this
     */
    public function withQueryParam(string $key, string $value = null) : self
    {
        $new = clone $this;
        parse_str($this->query, $result);

        if ($value === null) {
            unset ($result[$key]);
        } else {
            $result[$key] = $value;
        }
        $new->query = http_build_query($result);
        return $new;
    }

    /**
     * Return a new Url Instance with the params in parameter 1 set
     * as query parameter (additional to already set ones)
     *
     * <example>
     *  echo phore_parse_url("http://abc/?p1=val1")->withQueryParams(["p2"=>"val2"]);
     *
     *  => http://abc/?p1=val1&p2=val2
     * </example>
     *
     * @param array|null $params
     * @return PhoreUrl
     */
    public function withQueryParams(array $params = null) : self
    {
        $new = clone $this;
        if ($params === null) {
            $new->query = "";
            return $new;
        }
        parse_str($this->query, $result);

        foreach ($params as $key => $value)
            $result[$key] = $value;

        $new->query = http_build_query($result);
        return $new;
    }

    /**
     * Return the full url as string (duplicate of __toString())
     *
     * @return string
     */
    public function getAsString() : string
    {
        return (string)$this;
    }

    /**
     * Build the url and return it as string
     *
     * @return string
     */
    public function __toString()
    {
        $url = "";
        if ($this->scheme !== null)
            $url = "{$this->scheme}://";
        
        if ($this->user !== null || $this->pass !== null) {
            if ($this->pass === null) {
                $url .= urlencode($this->user) . "@";
            } else {
                $url .= urlencode($this->user) . ":" . urlencode($this->pass) . "@";
            }
        }
        $url .= $this->host;
        $url .= $this->path;
        if ($this->query !== null) {
            $url .= "?" . $this->query;
        }

        if ($this->fragment !== null)
            $url .= "#" . $this->fragment;
        return $url;
    }

}
