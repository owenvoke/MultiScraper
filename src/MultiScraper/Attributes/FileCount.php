<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class FileCount
 */
class FileCount
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * FileCount constructor.
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if (!is_null($value)) {
            $this->set($value);
        }
    }

    /**
     * Get the attribute's value
     *
     * @param mixed $default
     * @return int|null|string
     */
    public function get($default = null)
    {
        return is_numeric($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = (int)$value;

        return $this;
    }
}