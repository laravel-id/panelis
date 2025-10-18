<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('user.email'))
            ->email()
            ->required()
            ->maxLength(100)
            ->unique(ignoreRecord: true);
    }

    public static function getLabel(): string
    {
        return __('user.profile');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(1)
                ->schema([
                    FileUpload::make('avatar')
                        ->default('https://dummyimage.com/900x700')
                        ->alignCenter()
                        ->label('')
                        ->disk('public')
                        ->directory('avatar')
                        ->visible('public')
                        ->moveFiles()
                        ->avatar()
                        ->nullable()
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                            return str(hash('crc32', Auth::id()))
                                ->append('.')
                                ->append($file->getClientOriginalExtension());
                        }),
                ]),

            Section::make(__('user.account'))
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),

            Section::make(__('user.profile'))
                ->relationship('profile')
                ->collapsed()
                ->schema([
                    TextInput::make('phone')
                        ->label(__('user.phone'))
                        ->tel()
                        ->maxLength(15)
                        ->nullable(),

                    Textarea::make('address')
                        ->label(__('user.address'))
                        ->rows(5)
                        ->nullable()
                        ->string(),
                ]),
        ]);
    }
}
