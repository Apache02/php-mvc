<?php

namespace fw;


class Framework
{
    private static $rootPath = null;

    public static function autoload($className)
    {
        $filepath = self::$rootPath . '/' . $className . '.php';
        $filepath = str_replace('\\', '/', $filepath);
        $filepath = str_replace('//', '/', $filepath);
        if ( !is_file($filepath) ) {
            throw new \fw\ClassNotFoundError('Class not found');
        }
        require_once $filepath;
    }

    public static function registerAutoload()
    {
        $rootPath = realpath(__DIR__ . '/..');
        self::$rootPath = $rootPath;
        spl_autoload_register([self::class, 'autoload'], true, true);
    }

    public static function slugToName ( $slug )
    {
        $words = explode('-', $slug);
        $words = array_map('ucfirst', $words);
        return implode('', $words);
    }

    public static function getRootPath ()
    {
        return self::$rootPath;
    }

}

Framework::registerAutoload();
