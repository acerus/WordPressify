<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/02/17
 * Time: 13:00
 */

namespace WPCCrawler\objects\enums;


use ReflectionClass;

/**
 * A base class for enums providing a few methods to check whether the defined enums are valid or not.
 *
 * @see http://stackoverflow.com/a/254543/2883487
 */
abstract class EnumBase {

    private static $constCacheArray = NULL;

    private static function getConstants() {
        if (self::$constCacheArray == NULL) self::$constCacheArray = [];

        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * Check if the enum name is valid.
     *
     * @param string $name
     * @param bool $strict
     * @return bool
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * Check if the enum value is valid.
     *
     * @param mixed $value
     * @param bool $strict
     * @return bool
     */
    public static function isValidValue($value, $strict = true) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
}