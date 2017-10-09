<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class FileSize
 */
class FileSize
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * FileSize constructor.
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
            if ($value = strtobytes($value)) {
                $this->value = $value;
            } else {
                throw new \Exception("Invalid FileSize", 1);
            }
        }

        return $this;
    }
}