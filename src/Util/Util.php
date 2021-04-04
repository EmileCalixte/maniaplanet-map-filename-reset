<?php


namespace App\Util;


abstract class Util
{
    public static function echoScriptUsage()
    {
        echo "Usage: php {$_SERVER['argv'][0]} <path>";
    }

    public static function getPathFromSlug(array $slug): string
    {
        return implode(DIRECTORY_SEPARATOR, $slug);
    }

    public static function getPathSlug(string $path): array
    {
        $realpath = realpath($path);

        if ($realpath === false) {
            throw new \InvalidArgumentException("$path is not a valid path");
        }

        return explode(DIRECTORY_SEPARATOR, $realpath);
    }

    public static function println($item) {
        echo $item;
        echo "\n";
    }
}
