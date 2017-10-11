<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Hash
 */
class Hash
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Hash constructor.
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
     * @param string $value
     * @return $this
     * @throws \Exception
     */
    public function set(string $value)
    {
        if (!preg_match('/^[a-f0-9]{40}$/i', $value)) {
            throw new \Exception("Invalid Hash", 1);
        }

        $this->value = strtolower($value);

        return $this;
    }
}