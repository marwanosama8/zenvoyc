<?php

namespace App;

use Spatie\Valuestore\Valuestore;

/**
 * Class Setting
 *
 * A utility class to manage application settings. It interacts with a Valuestore instance to
 * retrieve, set, and manipulate settings from different channels.
 *
 * @package App
 */
class Setting
{
    /**
     * The default path for the main settings file.
     *
     * @var string
     */
    protected static $path = 'main.json';

    /**
     * The directory path where setting files are stored.
     *
     * @var string
     */
    protected static $directory = 'settings/';

    /**
     * The current Valuestore instance.
     *
     * @var Valuestore|null
     */
    protected static $valuestore = null;

    /**
     * Switch to a specific channel for settings.
     *
     * This method changes the path to point to a specific settings file
     * named after the given channel.
     *
     * @param string $name The name of the channel.
     *
     * @return Setting Returns a new instance of the Setting class.
     */
    public static function Channel($name)
    {
        static::$path = static::$directory . $name . '.json';
        return new static;
    }

    /**
     * Get or create a Valuestore instance for the current channel.
     *
     * @return Valuestore The Valuestore instance for the current channel.
     */
    protected static function getValuestore()
    {
        if(static::$path === 'main.json') {
            return static::$valuestore = Valuestore::make(config_path(static::$directory . static::$path));
        }

        return static::$valuestore = Valuestore::make(config_path(static::$path));
    }

    /**
     * Retrieve a setting value by its key.
     *
     * @param string $key The key of the setting.
     * @param mixed  $default The default value to return if the key doesn't exist.
     *
     * @return mixed The setting value.
     */
    public static function get($key, $default = null)
    {
        return static::getValuestore()->get($key, $default);
    }

    /**
     * Set a setting value by its key.
     *
     * @param string $key   The key of the setting.
     * @param mixed  $value The value to set.
     *
     * @return Valuestore
     */
    public static function set($key, $value = null)
    {
        return static::getValuestore()->put($key, $value);
    }

    /**
     * Save multiple settings from an array.
     *
     * @param array $array An associative array of settings.
     *
     * @return Valuestore
     */
    public static function save($array)
    {
        return static::getValuestore()->put($array);
    }

    /**
     * Delete a setting by its key.
     *
     * @param string $key The key of the setting to delete.
     *
     * @return Valuestore
     */
    public static function delete($key)
    {
        return static::getValuestore()->forget($key);
    }
}
