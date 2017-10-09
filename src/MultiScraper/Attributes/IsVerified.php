<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class IsVerified
 */
class IsVerified
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * IsVerified constructor.
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
     * @return bool|null
     */
    public function get($default = null)
    {
        return is_bool($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $value ? true : false;

        return $this;
    }
}