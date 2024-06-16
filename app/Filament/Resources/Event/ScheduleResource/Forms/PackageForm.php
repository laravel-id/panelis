<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class PackageForm
{
    public static function schema(): array
    {
        return [
            Repeater::make(__('event.package'))
                ->relationship('packages')
                ->schema([
                    TextInput::make('title')
                        ->label(__('event.package_title'))
                        ->maxLength(250)
                        ->required(),

                    TextInput::make('price')
                        ->label(__('event.package_price'))
                        ->default(0)
                        ->numeric()
                        ->required(),

                    Textarea::make('description')
                        ->label(__('event.package_description'))
                        ->rows(5)
                        ->columnSpan(2),
                ])
                ->orderColumn('sort')
                ->reorderable()
                ->reorderableWithButtons(true)
                ->columns(2),
        ];
    }
}
