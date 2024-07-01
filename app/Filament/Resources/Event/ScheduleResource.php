<?php

namespace App\Filament\Resources\Event;

use App\Filament\Resources\Event\ScheduleResource\Forms\OrganizerForm;
use App\Filament\Resources\Event\ScheduleResource\Forms\PackageForm;
use App\Filament\Resources\Event\ScheduleResource\Forms\ReplicateForm;
use App\Filament\Resources\Event\ScheduleResource\Forms\ScheduleForm;
use App\Filament\Resources\Event\ScheduleResource\Pages;
use App\Filament\Resources\Event\ScheduleResource\Pages\ViewSchedule;
use app\Filament\Resources\Event\ScheduleResource\Widgets\ScheduleOverview;
use App\Filament\Resources\Event\TypeResource\Forms\TypeForm;
use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use App\Models\Event\Type;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.event');
    }

    public static function getLabel(): ?string
    {
        return __('event.schedule');
    }

    public static function getWidgets(): array
    {
        return [
            ScheduleOverview::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make(__('event.schedule'))
                        ->description(__('event.schedule_section_description'))
                        ->icon('heroicon-s-calendar')
                        ->schema(ScheduleForm::schema()),

                    Wizard\Step::make(__('event.organizer'))
                        ->description(__('event.organizer_section_description'))
                        ->icon('heroicon-s-building-office')
                        ->schema(OrganizerForm::schema()),

                    Wizard\Step::make(__('event.type'))
                        ->description(__('event.type_section_description'))
                        ->icon('heroicon-s-tag')
                        ->schema(TypeForm::schema()),

                    Wizard\Step::make(__('event.package'))
                        ->description(__('event.package_section_description'))
                        ->icon('heroicon-s-currency-dollar')
                        ->schema(PackageForm::schema()),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function (Schedule $schedule): string {
                return Pages\ViewSchedule::getUrl([$schedule->id]);
            })
            ->defaultSort('started_at')
            ->columns([
                TextColumn::make('title')
                    ->label(__('event.schedule_title'))
                    ->weight(FontWeight::Bold)
                    ->description(function (Schedule $schedule): ?string {
                        return Str::words($schedule->description, 10);
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('categories')
                    ->label(__('event.category'))
                    ->searchable(),

                ImageColumn::make('organizers.logo')
                    ->label(__('event.organizer'))
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                TextColumn::make('location')
                    ->icon('heroicon-s-map-pin')
                    ->url(function (Schedule $schedule): ?string {
                        if (! empty($schedule->metadata['location_url'])) {
                            return $schedule->metadata['location_url'];
                        }

                        return null;
                    })
                    ->label(__('event.schedule_location'))
                    ->sortable()
                    ->searchable(),

                ToggleColumn::make('is_virtual')
                    ->label(__('event.schedule_is_virtual')),

                TextColumn::make('started_at')
                    ->label(__('event.schedule_started_at'))
                    ->sortable()
                    ->dateTime(config('app.datetime_format'), get_timezone()),
            ])
            ->filters([
                TernaryFilter::make('is_virtual')
                    ->label(__('event.schedule_is_virtual')),

                SelectFilter::make('organizers')
                    ->label(__('event.organizer'))
                    ->relationship('organizers', 'name')
                    ->preload()
                    ->multiple()
                    ->options(Organizer::options()),

                SelectFilter::make('types')
                    ->relationship('types', 'title')
                    ->multiple()
                    ->searchable()
                    ->options(Type::options()),

                TrashedFilter::make(),

            ])
            ->actions([
                EditAction::make(),

                RestoreAction::make(),

                ActionGroup::make([
                    ReplicateAction::make()
                        ->form(ReplicateForm::make())
                        ->beforeReplicaSaved(function (ReplicateAction $action, Schedule $schedule, Schedule $replica, array $data): void {
                            ScheduleResource\Actions\ReplicateAction::beforeReplicate($schedule, $replica, $data);
                        })
                        ->after(function (Schedule $schedule, Schedule $replica, array $data): void {
                            ScheduleResource\Actions\ReplicateAction::afterReplicate($schedule, $replica, $data);
                        })
                        ->successRedirectUrl(function (Schedule $replica): string {
                            return ViewSchedule::getUrl(['record' => $replica]);
                        }),

                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
            'view' => Pages\ViewSchedule::route('/{record}'),
        ];
    }
}
