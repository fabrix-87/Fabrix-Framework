<?php

namespace System\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class Message implements MessageInterface{

    /**
     * @var string
     */
    protected $protocolVersion = "1.1";

    /**
     * @var array
     */
    protected static $validProtocolVersions = [
        '1.0',
        '1.1',
        '2.0',
        '2',
    ];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        // if is not a valid version throw an exception
        if(!in_array($version,$this->validProtocolVersions)){
            throw new InvalidArgumentException(
                'Invalid HTTP version. Must be one of: '.implode(', ',$this->validProtocolVersion)
            );
        }

        $copy = clone($this);
        $copy->protocolVersion = $version;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name) : bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name) : array
    {
        return $this->headers[$name] ?: [];
    }

    /**
     * Set a new header
     * 
     * @param string $name header field name to add.
     * @param string|string[] $value Header value(s)
     * 
     */
    private function setHeader($name, $value){
        $this->headers[$name] = is_array($value) ? $value : [$value];
    }

    /**
     * Add a new value to header
     * 
     * @param string $name header field name to add.
     * @param string|string[] $value Header value(s)
     * 
     */
    private function addHeader($name, $value){
        if(key_exists($name,$this->headers[$name]))
            $value = array_merge($this->headers[$name], $value);

        $this->headers[$name] = $value;
    }

    /**
     * Remove a value to header
     * 
     * @param string $name header field name to remove
     * 
     */
    private function removeHeader($name){
        if(key_exists($name,$this->headers[$name]))
            unset($this->headers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name) : string
    {
        return isset($this->headers[$name]) ? implode(', ', $this->headers[$name]) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $copy = clone($this);
        $copy->setHeader($name,$value);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $copy = clone($this);
        $copy->addHeader($name,$value);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $copy = clone($this);
        $copy->removeHeader($name);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody() : StreamInterface
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $copy = clone($this);
        $copy->body = $body;
        
        return $copy;
    }

}