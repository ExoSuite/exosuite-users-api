<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 2019-01-11
 * Time: 12:56
 */

namespace App\Services;


class ClassFinder
{
    //This value should be the directory that contains composer.json
    const appRoot = __DIR__ . "/../../";
    const indexesNamespace = "App\Models\Indexes";

    private static function getClassesInNamespace($namespace)
    {
        $files = scandir(self::getNamespaceDirectory($namespace));
        if (!$files) return null;

        $classes = array_map(function (string $file) use ($namespace) {
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);


        return array_filter($classes, function ($possibleClass) {
            return class_exists($possibleClass);
        });
    }

    private static function getDefinedNamespaces()
    {
        $composerJsonPath = self::appRoot . 'composer.json';
        $content = file_get_contents($composerJsonPath);
        if (!$content) return null;
        $composerConfig = json_decode($content);

        return (array)$composerConfig->autoload->{"psr-4"};
    }

    private static function getNamespaceDirectory($namespace)
    {
        $composerNamespaces = self::getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while ($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (array_key_exists($possibleNamespace, $composerNamespaces)) {
                return realpath(
                    self::appRoot
                    . $composerNamespaces[$possibleNamespace]
                    . implode('/', $undefinedNamespaceFragments)
                );
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return false;
    }

    public static function getIndexesClasses(): array
    {
        return self::getClassesInNamespace(self::indexesNamespace);
    }

}
