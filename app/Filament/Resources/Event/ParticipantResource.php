<?php

namespace App\Filament\Resources\Event;

use App\Actions\Events\Participants\Cancel;
use App\Actions\Events\Participants\ConfirmPayment;
use App\Enums\Participants\Status;
use App\Filament\Resources\Event\ParticipantResource\Forms\EmergencyContactForm;
use App\Filament\Resources\Event\ParticipantResource\Forms\ParticipantForm;
use App\Filament\Resources\Event\ParticipantResource\Pages;
use App\Filament\Resources\Event\ScheduleResource\Pages\ViewSchedule;
use App\Models\Event\Participant;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.event');
    }

    public static function getLabel(): ?string
    {
        return __('event.participant');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('event.participant_section'))
                    ->schema(ParticipantForm::make()),

                Section::make(__('event.participant_emergency_section'))
                    ->collapsible()
                    ->schema(EmergencyContactForm::make()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('schedule.title')
                    ->label(__('event.schedule_title'))
                    ->url(fn (Participant $record): string => ViewSchedule::getUrl(['record' => $record->schedule_id])),

                TextColumn::make('package.title')
                    ->label(__('event.schedule_package')),

                TextColumn::make('bib')
                    ->label(__('event.participant_bib'))
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('event.participant_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('transaction.total')
                    ->label(__('event.participant_payment_total'))
                    ->sortable()
                    ->alignEnd()
                    ->description(fn (?Participant $record): ?string => $record->transaction?->status?->label())
                    ->formatStateUsing(fn (?Participant $record): ?string => Number::money($record->transaction?->total)),

                TextColumn::make('status')
                    ->label(__('event.participant_status'))
                    ->formatStateUsing(fn (Participant $record): string => $record->status->label())
                    ->badge()
                    ->color(function (Status $state): string {
                        return match ($state->value) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'expired' => 'danger',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('local_created_at')
                    ->label(__('ui.created_at'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('event.participant_status'))
                    ->multiple()
                    ->options(Status::options()),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('public_status')
                        ->icon('heroicon-o-link')
                        ->label(__('event.participant_status'))
                        ->url(fn (Participant $record): string => route('participant.status', $record->ulid)),

                    Action::make('confirm_payment')
                        ->color('warning')
                        ->icon('heroicon-o-banknotes')
                        ->label(__('event.btn_participant_payment_confirm'))
                        ->requiresConfirmation()
                        ->visible(function (Participant $record): bool {
                            return in_array($record->status, [
                                Status::Pending,
                                Status::OnHold,
                            ]);
                        })
                        ->action(function (Participant $record): void {
                            ConfirmPayment::run($record);
                        }),

                    Action::make('cancel')
                        ->color('danger')
                        ->icon('heroicon-o-archive-box-x-mark')
                        ->label(__('event.btn_participant_cancel'))
                        ->requiresConfirmation()
                        ->visible(function (Participant $record): bool {
                            return $record->status == Status::Pending;
                        })
                        ->action(function (Participant $record): void {
                            Cancel::run($record);
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([

                ]),
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
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
