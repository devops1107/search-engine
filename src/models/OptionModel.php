<?php

namespace spark\models;

/**
* Model for Options
*
* @package spark
*/
class OptionModel extends Model
{
    /**
     * @var string Table name
     */
    protected static $table = 'options';

    /**
     * @var string Base Query Key for CRUD
     */
    protected $queryKey = 'option_name';

    /**
     * @var array Loaded options
     */
    protected $options = [];

    /**
     * @var array Invalid option keys
     */
    protected $invalidOptions = [];

    /**
     * @var boolean Autoload status
     */
    protected $autoloaded = false;

    public function __construct($autoload = true)
    {
        if ($autoload) {
            $this->autoloadOptions();
        }
    }

    /**
     * Get a option by it's name
     *
     * @param  string $name     The name of the option
     * @param  mixed  $fallback Fallback value incase the option isn't present
     * @return mixed
     */
    public function get($name, $fallback = null)
    {
        // No No no. we wont waste SQL queries for multiple invalid calls
        if (in_array($name, $this->invalidOptions)) {
            return $fallback;
        }

        // return from the loaded options if present
        if (isset($this->options[$name])) {
            $value = $this->options[$name];
        } else {
            $query = $this->read($name, ['option_value']);

            if (!isset($query['option_value'])) {
            // Mark the option as invalid
                $this->invalidOptions[] = $name;
                return $fallback;
            }

            $value = $query['option_value'];
        }


        // set this to the loaded option as well for future calls
        $this->options[$name] = $value;

        return $value;
    }

    /**
     * Set a option's value by it's name
     *
     * @param  string  $name     The name of the option
     * @param  mixed   $value    The value of the option
     * @param  integer $autoload The autoload value (optional)
     * @return mixed
     */
    public function set($name, $value, $autoload = null)
    {
        $data = [
            'option_value' => $value
        ];

        // we only update when you want to
        if (is_int($autoload)) {
            $data['option_autoload'] = $autoload === 1 ? 1 : 0;
        }


        // so the option doesn't exist?
        if (!$this->exists($name)) {
            $data['option_name'] = $name;
            // no worries, we'd create one for ya
            return $this->create($data);
        } else {
            // we'll update it for ya
            $this->update($name, $data);
        }

        // Instant update on autoloaded array as well
        $this->options[$name] = $value;

        // "always true! you monster!" - heard ya, don't freak out. PDO would
        // throw an exception if anything goes wrong and you'll only reach here if everything is honky-dairy!
        return true;
    }

    /**
     * Get all loaded options
     *
     * @return array
     */
    public function getAll()
    {
        return $this->options;
    }

    /**
     * Autoload the options that are marked to be autoloaded
     *
     * @return integer
     */
    protected function autoloadOptions()
    {
        $loadedOpts = 0;

        // We'll autoload only once
        if ($this->autoloaded) {
            return $loadedOpts;
        }

        $sql = $this->select(['option_name', 'option_value'])->where('option_autoload', '=', 1);
        $stmt = $sql->execute();

        foreach ($stmt->fetchAll() as $option) {
            $name = $option['option_name'];
            $value = $option['option_value'];
            $this->options[$name] = $value;
            $loadedOpts++;
        }

        return $loadedOpts;
    }

    /**
     * Return JSON Decoded value from an option
     *
     * @param  string $key
     * @param  mixed  $fallback
     * @return mixed
     */
    public function getJsonValue($key, $fallback = [])
    {
        $value = $this->get($key, false);

        if (!is_string($value)) {
            return $fallback;
        }

        $value = json_decode($value, true);

        if (!is_array($value)) {
            return $fallback;
        }

        return $value;
    }
}
