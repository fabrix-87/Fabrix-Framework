<?php

namespace System\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** 
     * @var string
     */
    protected string $scheme = '';

    /** 
     * List of schemes accepted
     * 
     * @var array
     */
    protected static string $validSchemes = [
        '',
        'http',
        'https'
    ];

    /** 
     * @var string
     */
    protected string $user = '';

    /** 
     * @var string
     */
    protected string $password = '';

    /** 
     * @var string
     */
    protected string $host = '';

    /** 
     * @var int
     */
    protected $port = '';

    /** 
     * @var string
     */
    protected string $path = '';

    /** 
     * @var string
     */
    protected string $query = '';

    /** 
     * @var string
     */
    protected string $fragment = '';

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority() : string
    {
        $authority = ($this->getUserInfo() !== '') ? $this->getUserInfo().'@' : '';
        $authority .= $this->getHost();

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo() : string
    {
        $info = $this->user;
        if(is_string($this->password) and $this->password !== '')
            $info .= ':'.$this->password;

        return $info;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort() : ?int
    {
        return (!is_int($this->port) and $this->checkStandardPort()) ? null : $this->port;
    }

    /** 
     * Check if it's a standard for the current scheme
     * 
     * @return bool
     */
    private function checkStandardPort() : bool
    {
        return (($this->scheme === 'http' and $this->port === 80) or ($this->scheme === 'https' and $this->port === 443));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery() : string
    {
        return $this->query;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFragment() : string
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        $scheme = strtolower($scheme);
        if(!$this->hasValidScheme($scheme))
        {
            throw new InvalidArgumentException(
                'Invalid Http Scheme. Must be one of: '.implode(', ', $this->validScheme)
            );
        }
        
        $copy = clone($this);
        $copy->scheme = $scheme;
        return $copy;        
    }

    /**
     * Check if the scheme is valid or supported
     * 
     * @param string $scheme
     * @return bool
     */
    private function hasValidScheme($scheme) : bool
    {
        return in_array($scheme, $this->validScheme);
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $copy = clone($this);
        $copy->user = $user;
        $copy->password = $password;
        return $copy;  
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        $copy = clone($this);
        $copy->host = $this->filterHost($host);
        return $copy;        
    }

    /** 
     * Filter the hostname and normalize it
     * 
     * @param string $host
     * @return string The host normalized
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    private function filterHost($host) : string
    {
        if(!is_string($host))
        {
            throw new InvalidArgumentException('Invalid hostname.');
        }
        
        if(filter_var($host,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6))
            $host = '['.$host.']';

        return strtolower($host);
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        $copy = clone($this);
        $copy->port = $this->filterPort($port);
        return $copy;        
    }

    /** 
     * Filter the uri port.
     * A null value provided for the port is equivalent to removing the port
     * information.
     * 
     * @param null|int $port
     * @return null|int 
     * @throws \InvalidArgumentException for invalid port number.
     */
    private function filterPort($port) : ?int
    {
        if(!is_null($port) or (!is_int($port) or ($port < 1 and $port > 1023)))
            throw new InvalidArgumentException('Invalid Uri port. Must be null or an integer [1-1023]');

        return $port;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        $copy = clone($this);
        $copy->path = $this->filterPath($path);
        return $copy;        
    }

    /** 
     * Normalize the uri path.
     * Prevent double-encode of the characters
     * 
     * @param string $path
     * @return string
     * @throws \InvalidArgumentException for invalid path.
     */
    private function filterPath($path) : string
    {
        if(!is_string($path))
            throw new InvalidArgumentException('Invalid Uri path.');

        $path = explode('/',$path);
        foreach($path as $k => $p)
        {            
            $path[$k] = rawurlencode(rawurldecode($p));
        }
        return implode('/',$path);        
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        $copy = clone($this);
        $copy->query = $this->filterQuery($query);
        return $copy;  
    }

    /** 
     * Normalize the Uri query.
     * Prevent double-encode of the characters
     * 
     * @param string $query
     * @return string
     * @throws \InvalidArgumentException for invalid query.
     */
    private function filterQuery($query) : string
    {
        if(!is_string($query))
            throw new InvalidArgumentException('Invalid Uri path.');

        $query = ltrim($query,'?');
        
        $regex = '/(^([^=&?]+)=)|([\&]([^=&?]+)=)/';
        $matchValue = preg_split($regex, $query, -1, PREG_SPLIT_NO_EMPTY);	
        foreach($matchValue as $k => $p)
        {
            $matchValue[$k] = rawurlencode(rawurldecode($p));
        }
        
        preg_match_all($regex, $query, $out);
        
        $regex = '/(^([^=&?]+))|(([^=&?]+))/';
        $match = preg_replace_callback(
            $regex,
            function ($match) {
                return rawurlencode(rawurldecode($match[0]));
            },
            $out[0]
        );
        $filter = '';
        foreach($match as $k => $v){
            $filter.=$v.$matchValue[$k];
        }	
        return $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        
    }
}