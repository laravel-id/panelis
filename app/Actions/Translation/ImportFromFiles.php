<?php

namespace App\Actions\Translation;

use App\Models\Translation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Finder\SplFileInfo;

class ImportFromFiles
{
    use AsAction;

    public static function getAllFiles(): array
    {
        return collect(File::allFiles(base_path('lang/en')))
            ->mapWithKeys(function (SplFileInfo $file): array {
                $basename = $file->getBasename('.php');

                return [$basename => $basename];
            })
            ->toArray();
    }

    public function handle(?array $files = null): void
    {
        $files ??= static::getAllFiles();

        foreach ($files as $basename) {
            foreach (Arr::dot(__($basename)) as $key => $text) {
                $translation = Translation::query()
                    ->where(['group' => $basename, 'key' => $key])
                    ->first();

                $existingText = $translation?->text ?? [];

                $mergedText = array_merge($existingText, ['en' => $text]);

                Translation::query()->updateOrCreate(['group' => $basename, 'key' => $key], [
                    'is_system' => true,
                    'text' => $mergedText,
                    'updated_at' => now(),
                ],
                );
            }
        }
    }
}
