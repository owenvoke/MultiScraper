<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class MagnetLink
 */
class MagnetLink
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * MagnetLink constructor.
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
     * @param string|null $default
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
        if (preg_match('/^magnet:\?xt=urn:btih:([a-f0-9]{40}).+/i', trim($value))) {
            $this->value = $value;
        } else {
            throw new \Exception("Invalid MagnetLink (`{$value}`)", 1);
        }

        return $this;
    }
}