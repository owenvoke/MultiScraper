<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Category
 */
class Category
{
    /**
     * @var int|null|string|object
     */
    protected $value;
    /**
     * @var bool
     */
    public $site_specific;

    /**
     * Category constructor.
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
     * @return int|null|object|string
     */
    public function get($default = null)
    {
        if ($this->site_specific) {
            return (object)[
                'category' => $this->value,
                'site'     => $this->site_specific
            ];
        }

        return is_numeric($this->value) ? $this->value : $default;
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
        if (is_numeric($value)) {
            $this->value = (int)$value;
        } elseif (is_object($value) && preg_match('^[a-z0-9]+Category$', get_class($value))) {
            $this->value = $value;
        } else {
            throw new \Exception("Invalid Category", 1);
        }

        return $this;
    }
}