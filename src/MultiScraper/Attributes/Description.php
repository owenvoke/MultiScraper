<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Description
 */
class Description
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Description constructor.
     *
     * @param mixed $value
     * @throws \Exception
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
     * @return null|string
     */
    public function get($default = null)
    {
        return is_string($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param mixed $value
     * @return $this
     * @throws \Exception
     */
    public function set($value)
    {
        if (!is_string($value)) {
            throw new \Exception("Invalid Description", 1);
        }
        $this->value = trim($value);

        return $this;
    }
}