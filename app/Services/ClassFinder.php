<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 2019-01-11
 * Time: 12:56
 */

namespace App\Services;

/**
 * Class ClassFinder
 * @package App\Services
 */
class ClassFinder
{
    //This value should be the directory that contains composer.json
    private const appRoot = __DIR__ . "/../../";
    private const indexesNamespace = "App\Models\Indexes";

    /**
     * @return string[]
     */
    public static function getIndexesClasses(): array
    {
        return self::getClassesInNamespace(self::indexesNamespace);
    }

    /**
     * @param string $namespace
     *
     * @return string[]|null
     */
    private static function getClassesInNamespace(string $namespace): ?array
    {
        $namespaceDir = self::getNamespaceDirectory($namespace);

        if (is_bool($namespaceDir)) {
            return null;
        }

        $files = scandir($namespaceDir);

        if (!$files) {
            return null;
        }

        $classes = array_map(static function (string $file) use ($namespace) {
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        return array_filter($classes, static function ($possibleClass) {
            return class_exists($possibleClass);
        });
    }

    /**
     * @param string $namespace
     *
     * @return string|bool
     */
    private static function getNamespaceDirectory(string $namespace)
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

    /**
     * @return string[]|null
     */
    private static function getDefinedNamespaces(): ?array
    {
        $composerJsonPath = self::appRoot . 'composer.json';
        $content = file_get_contents($composerJsonPath);

        if (!$content) {
            return null;
        }

        $composerConfig = json_decode($content);

        return (array)$composerConfig->autoload->{"psr-4"};
    }
}
