<?php

class Utils {


    /**
     * Multi Dimensional Array Sorting
     */
    public static function array_sort_func ($a, $b = null)
    {
        static $keys;
        if ($b === null) return $keys = $a;
        foreach ($keys as $k)
        {
            if ($k[0] === '!')
            {
                $k = substr($k, 1);
                if ($a[$k] !== $b[$k])
                {
                    return is_numeric($a[$k])
                        ? $b[$k] - $a[$k]
                        : strcasecmp($b[$k], $a[$k]);
                }
            }
            else if ($a[$k] !== $b[$k])
            {
                return is_numeric($a[$k])
                    ? $a[$k] - $b[$k]
                    : strcasecmp($a[$k], $b[$k]);
            }
        }
        return 0;
    }
    public static function array_sort (&$array)
    {
        $keys = func_get_args();
        if (! $array) {
            return $keys;
        }
        array_shift($keys);
        Utils::array_sort_func($keys);
        usort($array, 'Utils::array_sort_func');
    }


    /**
     * Multi Dimensional Model Sorting (Experimental)
     */
    public static function model_sort_func ($a, $b = null)
    {
        static $keys;
        if ($b === null) return $keys = $a;
        foreach ($keys as $k)
        {
            if ($k[0] === '!')
            {
                $k = substr($k, 1);
                if ($a->$k !== $b->$k)
                {
                    return is_numeric($a->$k)
                        ? $b->$k - $a->$k
                        : strcasecmp($b->$k, $a->$k);
                }
            }
            else if ($a->$k !== $b->$k)
            {
                return is_numeric($a->$k)
                    ? $a->$k - $b->$k
                    : strcasecmp($a->$k, $b->$k);
            }
        }
        return 0;
    }
    public static function model_sort (&$array)
    {
        $keys = func_get_args();
        if (! $array) {
            return $keys;
        }
        array_shift($keys);
        Utils::model_sort_func($keys);
        usort($array, 'Utils::model_sort_func');
    }


    /**
     * Normalize Bytes
     */
    public static function normalize_bytes ($bytes, $precision = 2, $html = false)
    {
        $format = function ($metric, $html) {
            if ( ! $html)
            {
                return $metric;
            }
            return ' <span class="text-muted">' . $metric . '</span>';
        };
        if ($bytes < 1024 * 1024)
        {
            return number_format($bytes / 1024, $precision) . $format('kb', $html);
        }
        if ($bytes < 1024 * 1024 * 1024)
        {
            return number_format($bytes / (1024 * 1024), $precision) . $format('mb', $html);
        }
        if ($bytes < 1024 * 1024 * 1024 * 1024)
        {
            return number_format($bytes / ( 1024 * 1024 * 1024), $precision) . $format('gb', $html);
        }
        return $bytes;
    }


    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    public static function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }

}
