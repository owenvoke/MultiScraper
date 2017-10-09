<?php

namespace YeTii\MultiScraper\Attributes;

class Tracker
{
    protected $value;

    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->set($value);
        }
    }

    public function get($default = null)
    {
        return is_string($this->value) ? $this->value : $default;
    }

    public function set(string $value)
    {
        if (preg_match('/((?:http|https|udp|tcp):\/\/(?:[a-z0-9\-]+\.)+(?:[a-z0-9]+)(?::[\d]+){0,1}(?:\/[^\s]+){0,1})/i', $value)) {
            $this->value = $value;
        } else {
            throw new \Exception("Invalid Tracker: (`{$value}`)", 1);
        }

        return $this;
    }
}