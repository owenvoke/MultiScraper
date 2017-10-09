<?php

namespace YeTii\MultiScraper\Attributes;

class IsVerified
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
        return is_bool($this->value) ? $this->value : $default;
    }

    public function set($value)
    {
        $this->value = $value ? true : false;

        return $this;
    }
}