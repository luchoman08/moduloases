<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitba64dff0a17789786aec8dd326d52142
{
    public static $files = array (
        '094883ee9da9e6fabd95b86a5ef61b72' => __DIR__ . '/..' . '/latitude/latitude/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Latitude\\QueryBuilder\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Latitude\\QueryBuilder\\' => 
        array (
            0 => __DIR__ . '/..' . '/latitude/latitude/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitba64dff0a17789786aec8dd326d52142::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitba64dff0a17789786aec8dd326d52142::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}