<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Models\Transaction\Bank;
use App\Services\Payments\Factory;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;

class SettingForm
{
    public static function make(): array
    {
        $payment = app(Factory::class);

        return [
            Toggle::make('metadata.registration')
                ->label(__('event.enable_registration'))
                ->live(),

            Fieldset::make(__('event.payment_setting'))
                ->columns(1)
                ->disabled(fn (Get $get): bool => ! $get('metadata.registration'))
                ->schema([
                    Select::make('metadata.bank_id')
                        ->label(__('event.schedule_bank_destination'))
                        ->options(Bank::options())
                        ->searchable()
                        ->disabled(fn (Get $get): bool => ! $get('metadata.registration'))
                        ->exists(Bank::class, 'id')
                        ->prefixAction(
                            Action::make('sync_banks')
                                ->iconButton()
                                ->icon('heroicon-s-arrow-path')
                                ->after(function (): void {
                                    Notification::make('synced')
                                        ->success()
                                        ->title(__('transaction.bank_synced'))
                                        ->send();
                                })
                                ->action(function (Action $action) use ($payment): void {
                                    foreach ($payment->getRegisteredBanks() as $bank) {
                                        Bank::query()->updateOrCreate([
                                            'bank_code' => $bank->getCode(),
                                            'vendor_id' => $bank->getId(),
                                            'vendor' => $payment->getVendor(),
                                            'account_number' => $bank->getAccountNumber(),
                                        ], [
                                            'bank_name' => $bank->getLabel(),
                                            'account_name' => $bank->getAccountName(),
                                            'is_active' => $bank->isActive(),
                                            'balance' => $bank->getBalance(),
                                        ],
                                        );
                                    }

                                    $action->dispatch('refresh');
                                }),
                        )
                        ->required(),

                    TextInput::make('metadata.expired_duration')
                        ->label(__('event.schedule_expired_duration'))
                        ->helperText(__('event.helper_expired_duration'))
                        ->required()
                        ->numeric(),
                ]),

            Fieldset::make(__('event.notification_setting'))
                ->columns(1)
                ->disabled(fn (Get $get): bool => ! $get('metadata.registration'))
                ->schema([
                    TextInput::make('metadata.notification_email')
                        ->label(__('event.notification_email'))
                        ->nullable()
                        ->email(),

                    TextInput::make('metadata.notification_slack_channel_id')
                        ->label(__('event.notification_slack_channel')),
                ]),
        ];
    }
}
