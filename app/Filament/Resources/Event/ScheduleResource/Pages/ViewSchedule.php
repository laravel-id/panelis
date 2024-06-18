<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use App\Models\Event\Package;
use App\Models\Event\Schedule;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
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
        $timezone = config('app.datetime_timezone', config('app.timezone'));

        return $infolist
            ->columns(3)
            ->schema([
                Section::make(__('event.schedule'))
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->columnSpan(2)
                            ->url(fn(Schedule $schedule): string => route('schedule.view', [
                                $schedule->slug,
                            ]))
                            ->label(__('event.schedule_title'))
                            ->openUrlInNewTab()
                            ->icon('heroicon-s-link')
                            ->iconPosition(IconPosition::After)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('description')
                            ->columnSpan(2)
                            ->hiddenLabel()
                            ->hidden(fn(Schedule $schedule): bool => empty($schedule->description))
                            ->label(__('event.schedule_description'))
                            ->markdown(),

                        TextEntry::make('started_at')
                            ->label(__('event.schedule_started_at'))
                            ->dateTime(config('app.datetime_format'), $timezone),

                        TextEntry::make('finished_at')
                            ->visible(fn(Schedule $schedule): bool => !empty($schedule->finished_at))
                            ->dateTime(config('app.datetime_format'), $timezone),

                        TextEntry::make('full_location')
                            ->html()
                            ->openUrlInNewTab()
                            ->label(__('event.schedule_location'))
                            ->icon(fn(Schedule $schedule): string => $schedule->is_virtual ? 'heroicon-s-globe-alt' : 'heroicon-s-map-pin')
                            ->formatStateUsing(function (Schedule $schedule): string {
                                if ($schedule->is_virtual) {
                                    return __('event.schedule_is_virtual');
                                }

                                return $schedule->full_location;
                            }),

                        RepeatableEntry::make('contacts')
                            ->label(__('event.schedule_contact'))
                            ->columns(3)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('event.schedule_contact_name')),

                                TextEntry::make('phone')
                                    ->label(__('event.schedule_contact_phone')),

                                TextEntry::make('email')
                                    ->label(__('event.schedule_contact_email')),
                            ]),

                        KeyValueEntry::make('metadata')
                            ->columnSpan(2)
                            ->label(__('event.schedule_metadata')),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('poster')
                            ->height(500)
                            ->hiddenLabel()
                            ->visible(fn(Schedule $schedule): bool => !empty($schedule->poster))
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

                        TextEntry::make('url')
                            ->label(__('event.schedule_url'))
                            ->url(fn(Schedule $schedule): ?string => $schedule->url)
                            ->formatStateUsing(fn(Schedule $schedule): string => Str::limit($schedule->url, 100)),

                        TextEntry::make('created_at')
                            ->label(__('ui.created_at'))
                            ->since(),

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
