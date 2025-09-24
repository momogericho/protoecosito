<?php

class AppAutoloader
{
    private static bool $registered = false;
    private static array $classMap = [];

    public static function register(array $directories): void
    {
        if (self::$registered) {
            return;
        }

        self::$classMap = self::buildClassMap($directories);
        spl_autoload_register([self::class, 'autoload']);
        self::$registered = true;
    }

    private static function buildClassMap(array $directories): array
    {
        $map = [];
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $className = $file->getBasename('.php');
                if (!isset($map[$className])) {
                    $map[$className] = $file->getPathname();
                }
            }
        }

        return $map;
    }

    private static function autoload(string $class): void
    {
        if (isset(self::$classMap[$class])) {
            require_once self::$classMap[$class];
        }
    }
}