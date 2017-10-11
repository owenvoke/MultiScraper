<?php

namespace YeTii\MultiScraper\Attributes;

/**
 * Class File
 */
class File
{
    /**
     * @var object|mixed
     */
    protected $value;

    /**
     * File constructor.
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
     * @return object|mixed
     */
    public function get($default = null)
    {
        return is_object($this->value) ? $this->value : $default;
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
        $this->value = new \stdClass;

        if (!isset($value->path) || !isset($value->file_size)) {
            throw new \Exception("Invalid File. Must have Path and File Size", 1);
        }
        $this->value->path = trim($value->path);
        $this->value->file_size = new FileSize($value->file_size);

        return $this;
    }
}