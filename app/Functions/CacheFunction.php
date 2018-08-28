<?php


namespace App\Functions;


class CacheFunction
{
    public static function makeCacheKey(...$args)
    {
        $key = [];
        foreach ($args as $arg) {
            if (is_array($arg) || is_object($arg)) {
                $key[] = json_encode($arg);
            } elseif (is_bool($arg)) {
                $key[] = $arg ? 1 : 0;
            } else {
                $key[] = strval($arg);
            }
        }

        return md5(implode('_', $key));
    }
}