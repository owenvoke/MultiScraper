<?php

namespace YeTii\MultiScraper\Attributes;

class FileSize
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
        return is_numeric($this->value) ? $this->value : $default;
    }

    public function set($value)
    {
        if (preg_match('/^\d+$/', trim($value))) {
            $this->value = (int)$value;
        } else {
            if ($value = strtobytes($value)) {
                $this->value = $value;
            } else {
                throw new \Exception("Invalid FileSize", 1);
            }
        }

        return $this;
    }
}