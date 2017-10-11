<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class User
 */
class User
{
    /**
     * @var object|string
     */
    protected $value;
    /**
     * @var bool
     */
    public $site_specific;

    /**
     * User constructor.
     *
     * @param string|null $value
     * @param mixed|null $instance
     * @throws \Exception
     */
    public function __construct($value = null, $instance = null)
    {
        if (!is_null($value)) {
            $this->set($value);
        }
    }

    /**
     * Get the attribute's value
     *
     * @param null|string|object $default
     * @return null|string|object
     */
    public function get($default = null)
    {
        if ($this->site_specific) {
            return (object)[
                'username' => $this->value,
                'site'     => $this->site_specific
            ];
        }

        return !is_null($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param string|object $value
     * @return $this
     * @throws \Exception
     */
    public function set($value)
    {
        if (is_string($value)) {
            $this->value = $value;
        } elseif (is_object($value) && preg_match('^[a-z0-9]+User$', get_class($value))) {
            $this->value = $value;
        } else {
            throw new \Exception("Invalid User", 1);
        }

        return $this;
    }
}