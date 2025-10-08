<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Filament\Resources\BranchResource\Enums\BranchPermission;
use Filament\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewBranch extends ViewRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(user_can(BranchPermission::Edit)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Section::make(__('branch.label'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('branch.name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),

                        TextEntry::make('slug')
                            ->label(__('ui.slug')),

                        Grid::make()
                            ->schema([
                                TextEntry::make('phone')
                                    ->label(__('branch.phone'))
                                    ->icon('heroicon-s-phone'),

                                TextEntry::make('email')
                                    ->label(__('branch.email'))
                                    ->icon('heroicon-s-at-symbol'),
                            ]),

                        TextEntry::make('address')
                            ->label(__('branch.address')),

                        KeyValueEntry::make('metadata')
                            ->label(__('branch.metadata')),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('logo')
                            ->hiddenLabel()
                            ->alignCenter()
                            ->disk('public'),

                        Fieldset::make(__('branch.managers'))
                            ->schema([
                                TextEntry::make('users.name')
                                    ->hiddenLabel()
                                    ->bulleted(),
                            ]),

                        TextEntry::make('user.name')
                            ->label(__('branch.owner')),

                        TextEntry::make('created_at')
                            ->label(__('ui.created_at'))
                            ->since()
                            ->dateTimeTooltip(get_datetime_format(), get_timezone()),

                        TextEntry::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->since()
                            ->dateTimeTooltip(get_datetime_format(), get_timezone()),
                    ]),
            ]);
    }
}
