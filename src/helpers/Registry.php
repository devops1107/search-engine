<?php

namespace spark\helpers;

/**
* Registry
*
* @package spark
*/
class Registry
{
    /**
     * Storage for registry
     *
     * @var array
     */
    protected $storage = [];

    /**
     * Readonly keys
     *
     * @var array
     */
    protected $readOnly = [];

    /**
     * Create a new registry
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->storage = $data;
    }

    /**
     * Read from registry
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function read($key, $default = null)
    {
        return DotArray::get($this->storage, $key, $default);
    }

    /**
     * Read property from registry
     *
     * @param  string $name
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function prop($name, $key, $default = null)
    {
        if ($object = $this->get($name)) {
            return array_reduce(explode('.', $key), function ($obj, $prop) use ($default) {
                if (is_object($obj) && property_exists($obj, $prop)) {
                    return $obj->$prop;
                }
                return $default;
            }, $object);
        }

        return $default;
    }

    /**
     * Store a key-value pair to registry
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  boolean $readOnly
     * @return boolean
     */
    public function store($key, $value, $readOnly = false)
    {
        if ($this->isReadOnly($key)) {
            return false;
        }

        if ($readOnly) {
            $this->readOnly[] = $key;
        }

        return DotArray::set($this->storage, $key, $value);
    }

    public function isReadOnly($key)
    {
        return in_array($key, $this->readOnly);
    }

    /**
     * Delete from registry
     *
     * @param  string $key
     * @return boolean
     */
    public function delete($key)
    {
        return DotArray::delete($this->storage, $key);
    }

    /**
     * Check if registry has a certain key
     *
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return DotArray::exists($this->storage, $key);
    }
}
