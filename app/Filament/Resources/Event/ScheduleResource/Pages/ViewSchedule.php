<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Enums\NotificationChannels;
use App\Filament\Resources\Event\ScheduleResource;
use App\Models\Event\Event;
use App\Models\Event\Package;
use App\Models\Event\Schedule;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ViewSchedule extends ViewRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\ActionGroup::make([
                Actions\ReplicateAction::make()
                    ->form(ScheduleResource\Forms\ReplicateForm::make())
                    ->beforeReplicaSaved(function (Actions\ReplicateAction $action, Schedule $schedule, Schedule $replica, array $data): void {
                        ScheduleResource\Actions\ReplicateAction::beforeReplicate($schedule, $replica, $data);
                    })
                    ->after(function (Schedule $schedule, Schedule $replica, array $data): void {
                        ScheduleResource\Actions\ReplicateAction::afterReplicate($schedule, $replica, $data);
                    })
                    ->successRedirectUrl(function (Schedule $replica): string {
                        return ViewSchedule::getUrl(['record' => $replica]);
                    }),

                Action::make('invite_user')
                    ->label(__('event.btn_invite_user'))
                    ->color('primary')
                    ->modalWidth(MaxWidth::Medium)
                    ->icon('heroicon-s-user')
                    ->form([
                        Select::make('users')
                            ->options(
                                User::query()
                                    ->where('id', '!=', Auth::id())
                                    ->pluck('name', 'id'),
                            )
                            ->searchable()
                            ->multiple()
                            ->required(),

                        CheckboxList::make('channels')
                            ->label(__('ui.notification_channels'))
                            ->options(NotificationChannels::options())
                            ->nullable(),
                    ])
                    ->action(function (array $data): void {
                        $this->record->users()->syncWithoutDetaching(
                            collect($data['users'])
                                ->mapWithKeys(fn (string $userId): array => [$userId => [
                                    'channels' => json_encode($data['channels']),
                                ]])
                                ->all(),
                        );

                        Notification::make('user_invited')
                            ->title(__('event.schedule_user_invited'))
                            ->success()
                            ->send();

                    }),

                Actions\DeleteAction::make()
                    ->after(function (Schedule $schedule): void {
                        Event::query()->where('id', $schedule->id)->delete();
                    }),
            ]),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $dateFormat = get_datetime_format();
        $timezone = get_timezone();

        return $infolist
            ->columns(3)
            ->schema([
                Section::make(__('event.schedule'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->columnSpan(2)
                            ->url(fn (Schedule $schedule): string => route('schedule.view', [
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
                            ->hidden(fn (Schedule $schedule): bool => empty($schedule->description))
                            ->label(__('event.schedule_description'))
                            ->markdown(),

                        Grid::make()
                            ->schema([
                                TextEntry::make('started_at')
                                    ->label(__('event.schedule_started_at'))
                                    ->icon('heroicon-s-calendar')
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->dateTime($dateFormat, $timezone),

                                TextEntry::make('finished_at')
                                    ->label(__('event.schedule_finished_at'))
                                    ->icon('heroicon-s-calendar')
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->visible(fn (Schedule $schedule): bool => ! empty($schedule->finished_at))
                                    ->dateTime($dateFormat, $timezone),

                                TextEntry::make('full_location')
                                    ->html()
                                    ->openUrlInNewTab()
                                    ->label(__('event.schedule_location'))
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->icon(fn (Schedule $schedule): string => $schedule->is_virtual ? 'heroicon-s-globe-alt' : 'heroicon-s-map-pin')
                                    ->formatStateUsing(function (Schedule $schedule): string {
                                        if ($schedule->is_virtual) {
                                            return __('event.schedule_is_virtual');
                                        }

                                        return $schedule->full_location;
                                    }),
                            ]),

                        RepeatableEntry::make('contacts')
                            ->label(__('event.schedule_contact'))
                            ->columns(3)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->icon('heroicon-s-user')
                                    ->label(__('event.schedule_contact_name')),

                                TextEntry::make('phone')
                                    ->icon('heroicon-s-phone')
                                    ->label(__('event.schedule_contact_phone')),

                                TextEntry::make('email')
                                    ->icon('heroicon-s-envelope')
                                    ->label(__('event.schedule_contact_email')),
                            ]),

                        KeyValueEntry::make('metadata')
                            ->columnSpan(2)
                            ->label(__('event.schedule_metadata')),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('organizers.name')
                            ->label(__('event.organizer'))
                            ->bulleted(),

                        TextEntry::make('categories')
                            ->label(__('event.schedule_category')),

                        TextEntry::make('url')
                            ->label(__('event.schedule_url'))
                            ->url(fn (Schedule $schedule): ?string => $schedule->url)
                            ->formatStateUsing(fn (Schedule $schedule): string => Str::limit($schedule->url, 100)),

                        TextEntry::make('external_url')
                            ->label(__('event.schedule_alias_url'))
                            ->url(fn (Schedule $schedule): ?string => $schedule->external_url)
                            ->default('-'),

                        TextEntry::make('created_at')
                            ->label(__('ui.created_at'))
                            ->since($timezone),

                        TextEntry::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->since($timezone),
                    ]),

                Section::make(__('event.package'))
                    ->schema([
                        RepeatableEntry::make('packages')
                            ->columns()
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('event.package_title')),

                                TextEntry::make('price')
                                    ->label(__('event.package_price'))
                                    ->formatStateUsing(function (Package $package): string {
                                        return Number::money($package->price);
                                    }),

                                TextEntry::make('started_at')
                                    ->label(__('event.package_started_at'))
                                    ->dateTime($dateFormat, $timezone),

                                TextEntry::make('ended_at')
                                    ->label(__('event.package_ended_at'))
                                    ->dateTime($dateFormat, $timezone),

                                TextEntry::make('description')
                                    ->label(__('event.package_description'))
                                    ->markdown()
                                    ->default('-'),
                            ]),
                    ]),
            ]);
    }
}
