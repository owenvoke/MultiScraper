<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class DateCreated
 */
class DateCreated
{
    /**
     * @var int|null|string
     */
    protected $value;

    /**
     * DateCreated constructor.
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
     * @throws \Exception
     */
    public function set($value)
    {
        if (preg_match('/^\d+$/', trim($value))) {
            $this->value = (int)$value;
        } else {
            $value2 = strtotime($value);
            if ($value2) {
                $this->value = $value2;
            } else {
                throw new \Exception("Invalid DateCreated (`{$value}`)", 1);
            }
        }

        return $this;
    }
}