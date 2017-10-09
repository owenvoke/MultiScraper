<?php

namespace YeTii\MultiScraper\Attributes;

class Hash
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
        if (!preg_match('/^[a-f0-9]{40}$/i', $value)) {
            throw new \Exception("Invalid Hash", 1);
        }

        $this->value = strtolower($value);

        return $this;
    }
}