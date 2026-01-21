<?php

namespace App\Filament\Clusters\Settings\Forms\General;

use App\Enums\Disk;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageForm
{
    public static function schema(): array
    {
        return [
            FileUpload::make('app.logo')
                ->label(__('setting.general.logo'))
                ->disk(Disk::Public->value)
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    return sprintf('logo-%s.%s', Str::slug(config('app.name')), $file->getClientOriginalExtension());
                })
                ->imageEditor()
                ->image(),

            FileUpload::make('app.favicon')
                ->label(__('setting.general.favicon'))
                ->disk(Disk::Public->value)
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    return 'favicon.'.$file->getClientOriginalExtension();
                })
                ->imageEditor()
                ->image(),

            Toggle::make('app.use_logo_in_panel')
                ->label(__('setting.general.use_logo_in_panel'))
                ->required(),
        ];
    }
}
