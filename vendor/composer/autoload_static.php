<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4ab9243b15913700d6a2df702215775d
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RepoScribe\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RepoScribe\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4ab9243b15913700d6a2df702215775d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4ab9243b15913700d6a2df702215775d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4ab9243b15913700d6a2df702215775d::$classMap;

        }, null, ClassLoader::class);
    }
}
