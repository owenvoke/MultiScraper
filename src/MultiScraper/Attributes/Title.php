<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Title
 */
class Title
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Title constructor.
     *
     * @param string|null $value
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
     * @param null|string $default
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
     */
    public function set(string $value)
    {
        $this->value = trim($value);
        if (strlen($value) > 256) {
            substr($value, 0, 256);
        }

        return $this;
    }
}