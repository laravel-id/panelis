<?php

namespace Panelis\Support;

class ModuleManager
{
    public static function getModules(): array
    {
        return collect(glob(base_path('modules/*/module.json')))
            ->map(fn ($path) => json_decode(file_get_contents($path), true))
            ->toArray();
    }

    public static function getResources(): array
    {
        return collect(glob(base_path('modules/*/Panel/Resources'), GLOB_ONLYDIR))
            ->map(function (string $directory): array {
                $module = basename(dirname(dirname($directory)));

                return [
                    'path' => $directory,
                    'namespace' => "Modules\\{$module}\\Panel\\Resources",
                ];
            })
            ->values()
            ->toArray();
    }

    public static function getClusters(): array
    {
        return collect(glob(base_path('modules/*/Panel/Clusters'), GLOB_ONLYDIR))
            ->map(function (string $directory): array {
                $module = basename(dirname(dirname($directory)));
                $namespace = "Modules\\{$module}\\Panel\\Clusters";

                return [
                    'path' => $directory,
                    'namespace' => $namespace,
                ];
            })
            ->toArray();
    }

    public static function register(): void
    {
        foreach (static::getModules() as $module) {
            if (! empty($module['provider'])) {
                app()->register($module['provider']);
            }
        }
    }
}
