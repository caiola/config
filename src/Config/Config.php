<?php

namespace Caiola;

/**
 * Configuration class that allows to read environment variables
 *
 * Class Config
 * @package Caiola
 */

class Config
{
    static protected $instance        = null;    // Singleton instance
    static private   $public          = array(); // Public variables
    static private   $env             = array(); // Environment variables
    static public    $use_environment = true;    // Use and read environment variables
    static public    $immutable       = true;    // If immutable is true then we cannot override environment variables

    /**
     * Config constructor.
     */
    public function __construct() {
    }

    /**
     * Clone method
     */
    public function __clone() {
    }

    /**
     * Get instance (singleton)
     *
     * @return Config
     */
    public static function getInstance() {
        if (!isset(static::$instance)) {
            self::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Get all configurations
     *
     * @return array
     */
    public static function all() {
        return array(
            'public' => self::$public,
            'env'    => self::$env
        );
    }

    /**
     * Append an array of configurations
     *
     * @param array $values Public values to append
     */
    public static function set(array $values) {
        // We could do self::$public += $values; but we want to be able to use environment variables
        // This way, it calls __get() method and properly defines the variables if the same name on ENV is found
        foreach ($values as $key => $value) {
            self::getInstance()->$key = $value;
        }
    }

    /**
     * Get value from a key configuration
     *
     * @param string $key Key name to define
     * @return null|string
     */
    public static function getPublic($key) {
        return isset(self::$public[$key]) ? self::$public[$key] : null;
    }

    /**
     * Define a key configuration with the specified value
     *
     * @param string $key Key name to obtain
     * @return null|string
     */
    public static function setPublic($key, $value) {
        self::$public[$key] = $value;
    }

    /**
     * Get configuration from environment variables and from local configured environment
     *
     * @param $key
     * @return string
     */
    public function __get($key) {
        switch (true) {
            case self::$use_environment === false:
                return isset(self::$public[$key]) ? self::$public[$key] : null;
            case self::$use_environment === false && array_key_exists($key, self::$env):
                return self::$env[$key];
            case self::$use_environment === false && array_key_exists($key, $_ENV):
                return $_ENV[$key];
            case self::$use_environment === false && array_key_exists($key, $_SERVER):
                return $_SERVER[$key];
            default:
                $env = getenv($key);
                $value = isset(self::$public[$key]) ? self::$public[$key] : null;

                return $env === false && self::$use_environment === false ? $value : $env;
        }
    }

    /**
     * Set the configuration
     *
     * @param string $key Name of the key
     * @param        $value
     */
    public function __set($key, $value) {
        switch (true) {
            case self::$use_environment === false:
                self::$public[$key] = $value;

                return;
            case array_key_exists($key, $_ENV):
                self::$env[$key] = $value;

                return;
            case array_key_exists($key, $_SERVER):
                self::$env[$key] = self::$immutable ? $_SERVER[$key] : $value;

                return;
            default:
                $env = getenv($key);
                if ($env === false) {
                    self::$public[$key] = $value;
                } else {
                    if (self::$use_environment) {
                        self::$env[$key] = self::$immutable ? $env : $value;
                    } else {
                        self::$public[$key] = self::$immutable ? $env : $value;
                    }
                }
        }
    }

    /**
     * Check if a key if defined
     *
     * @param string $key Name of the key
     * @return bool
     */
    public function __isset($key) {
        return isset(self::$public[$key]);
    }

    /**
     * Get a value from an object or an array.  Allows the ability to fetch a nested value from a
     * heterogeneous multidimensional collection using dot notation.
     *
     * Example:
     *              $grandchild = get_value( $data, 'parent.child.grandchild' );
     *
     *              $data['parent']->child['grandchild'];
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getByKey($key, $default = null) {
        $all_data = array(
            'env'    => self::$env,
            'public' => self::$public
        );

        $value = $default;

        // Loop through each visibility (env, public) to get the values
        foreach ($all_data as $env => $data) {
            if (is_array($data) && array_key_exists($key, $data)) {
                return $data[$key];
            }

            if (is_object($data) && property_exists($data, $key)) {
                return $data->$key;
            }

            $segments = explode('.', $key);
            foreach ($segments as $segment) {
                if (is_array($data) && array_key_exists($segment, $data)) {
                    $value = $data = $data[$segment];
                } else {
                    if (is_object($data) && property_exists($data, $segment)) {
                        $value = $data = $data->$segment;
                    } else {
                        $value = $default;
                        break;
                    }
                }
            }

            if ($value !== $default) {
                return $value;
            }
        }

        return $value;
    } // END :: getByKey()

}
