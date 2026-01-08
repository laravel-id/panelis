<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Filament\Resources\BranchResource\Enums\BranchPermission;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

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

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Section::make(__('branch.label'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('branch.name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),

                        TextEntry::make('slug')
                            ->label(__('ui.slug')),

                        Grid::make()
                            ->schema([
                                TextEntry::make('phone')
                                    ->label(__('branch.phone'))
                                    ->icon(Heroicon::Phone),

                                TextEntry::make('email')
                                    ->label(__('branch.email'))
                                    ->icon(Heroicon::AtSymbol),
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
                            ->dateTimeTooltip(get_datetime_format()),

                        TextEntry::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->since()
                            ->dateTimeTooltip(get_datetime_format()),
                    ]),
            ]);
    }
}
