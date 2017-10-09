<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class Trackers
 */
class Trackers
{
    /**
     * @var array
     */
    protected $value;

    /**
     * Trackers constructor.
     *
     * @param null|array $value
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
     * @param null|array $default
     * @return array|null
     */
    public function get($default = null)
    {
        return is_array($this->value) ? $this->value : $default;
    }

    /**
     * Set the attribute's value
     *
     * @param array $value
     * @return $this
     */
    public function set(array $value)
    {
        $this->value = [];

        foreach ($value as $tracker) {
            $this->add($tracker);
        }

        return $this;
    }

    /**
     * Add a tracker to the attribute
     *
     * @param array|mixed $value
     * @return $this
     */
    public function add($value)
    {
        if (is_array($value)) {
            foreach ($value as $value2) {
                $this->add($value2);
            }
        } else {
            if (!is_array($this->value)) {
                $this->value = [];
            }
            if (!in_array($value, $this->value)) {
                $this->value[] = new Tracker(preg_match('/%3A/i', $value) ? urldecode($value) : $value);
            }
        }

        return $this;
    }

    /**
     * Remove a tracker from the attribute
     *
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        if (isset($this->value[$key])) {
            unset($key);
        }

        return $this;
    }
}