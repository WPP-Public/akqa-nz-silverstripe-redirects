<?php

namespace Heyday\Redirects;

class Redirect
{
    /** @var string */
    protected $from;

    /** @var string */
    protected $to;
    
    /** @var int */
    protected $statusCode = 301;

    /**
     * @param string $from
     * @param string $to
     */
    public function __construct($from, $to, $statusCode = null)
    {
        $this->from = $from;
        $this->to = $to;
        if (is_int($statusCode)) {
            $this->statusCode = $statusCode;
        }
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function match($url)
    {
        $form = $this->formatUrl($this->from);
        $current = $this->formatUrl($url);
        $to = $this->formatUrl($this->to);
        return $form === $current
            && $form !== $to;
    }

    /**
     * @param string $url
     * @return string
     */
    public function formatUrl($url)
    {
        return trim(
            strtolower($url),
            '/'
        );
    }
}