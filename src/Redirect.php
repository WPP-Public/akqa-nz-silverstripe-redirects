<?php

namespace Heyday\Redirects;

class Redirect
{
    /** @var string */
    protected $from;

    /** @var string */
    protected $to;

    /** @var int */
    protected $statusCode;

    /**
     * @param string $from
     * @param string $to
     * @param int $statusCode
     */
    public function __construct($from, $to, $statusCode = 301)
    {
        $this->from = self::formatUrl($from);
        $this->to = self::formatUrl($to);
        $this->statusCode = $statusCode;
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
        return ((isset($this->statusCode) && !empty($this->statusCode)) ? $this->statusCode : 301);
    }

    /**
     * @param string $url
     * @return bool
     */
    public function match($url)
    {
        $from = self::formatUrl($this->from);

        return (($from === self::formatUrl($url)) && ($from !== self::formatUrl($this->to)));
    }

    /**
     * @param string $url
     * @return string
     */
    public static function formatUrl($url)
    {
        return trim(
            strtolower($url),
            '/'
        );
    }
}