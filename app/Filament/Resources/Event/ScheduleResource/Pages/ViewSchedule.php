<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use App\Models\Event\Package;
use App\Models\Event\Schedule;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ViewSchedule extends ViewRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Section::make(__('event.schedule'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->url(fn (Schedule $schedule): string => route('schedule.view', [
                                $schedule->started_at->format('Y'),
                                $schedule->slug,
                            ]))
                            ->label(__('event.schedule_title'))
                            ->openUrlInNewTab()
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->hidden(fn (Schedule $schedule): bool => empty($schedule->description))
                            ->label(__('event.schedule_description'))
                            ->markdown(),

                        TextEntry::make('started_at')
                            ->label(__('event.schedule_started_at'))
                            ->date(config('app.datetime_format')),

                        TextEntry::make('finished_at')
                            ->visible(fn (Schedule $schedule): bool => ! empty($schedule->finished_at))
                            ->date(),

                        IconEntry::make('is_virtual')
                            ->boolean()
                            ->label(__('event.schedule_is_virtual')),

                        TextEntry::make('full_location')
                            ->visible(fn (Schedule $schedule): bool => ! $schedule->is_virtual)
                            ->html()
                            ->openUrlInNewTab()
                            ->icon('heroicon-s-map-pin')
                            ->label(__('event.schedule_location')),

                        RepeatableEntry::make('contacts')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('phone'),
                                TextEntry::make('email'),
                            ]),

                        KeyValueEntry::make('metadata')
                            ->label(__('event.schedule_metadata')),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('poster')
                            ->height(500)
                            ->hiddenLabel()
                            ->visible(fn (Schedule $schedule): bool => ! empty($schedule->poster))
                            ->alignment(Alignment::Center)
                            ->extraImgAttributes([
                                'alt' => 'Poster',
                                'loading' => 'lazy',
                            ]),

                        TextEntry::make('organizers.name')
                            ->label(__('event.organizer'))
                            ->bulleted(),

                        TextEntry::make('categories')
                            ->label(__('event.schedule_category')),

                        TextEntry::make('created_at')
                            ->label(__('ui.created_at'))
                            ->since(),

                        TextEntry::make('url')
                            ->label(__('event.schedule_url'))
                            ->url(fn (Schedule $schedule): ?string => $schedule->url)
                            ->formatStateUsing(fn (Schedule $schedule): string => Str::limit($schedule->url, 100)),

                        TextEntry::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->since(),
                    ]),

                Section::make(__('event.package'))
                    ->schema([
                        RepeatableEntry::make('packages')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('event.package_title')),

                                TextEntry::make('price')
                                    ->label(__('event.package_price'))
                                    ->formatStateUsing(function (Package $package): string {
                                        return Number::money($package->price);
                                    }),

                                TextEntry::make('description')
                                    ->label(__('event.package_description')),
                            ]),
                    ]),
            ]);
    }
}
