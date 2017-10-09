<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Id
 */
class Id
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Id constructor.
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
     * @return null|mixed
     */
    public function get($default = null)
    {
        return !is_null($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $value;

        return $this;
    }
}