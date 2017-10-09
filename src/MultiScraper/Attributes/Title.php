<?php

namespace YeTii\MultiScraper\Attributes;

class Title
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
        $this->value = trim($value);
        if (strlen($value) > 256) {
            substr($value, 0, 256);
        }

        return $this;
    }
}