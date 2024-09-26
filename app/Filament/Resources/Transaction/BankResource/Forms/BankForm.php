<?php

namespace App\Filament\Resources\Transaction\BankResource\Forms;

use App\Services\Payments\Factory;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class BankForm
{
    public static function make(): array
    {
        $banks = app(Factory::class)->getBanks();

        $codes = [];
        foreach ($banks as $bank) {
            $codes[$bank->getCode()] = $bank->getLabel();
        }

        return [
            Select::make('code')
                ->label(__('transaction.bank_type'))
                ->required()
                ->options($codes),

            Grid::make()
                ->schema([
                    TextInput::make('username')
                        ->label(__('transaction.bank_username'))
                        ->helperText(__('transaction.helper_bank_credential_not_stored'))
                        ->required(),

                    TextInput::make('password')
                        ->label(__('transaction.bank_password'))
                        ->password()
                        ->required(),
                ]),

            TextInput::make('account_name')
                ->label(__('transaction.bank_account_name'))
                ->required(),

            TextInput::make('account_number')
                ->label(__('transaction.bank_account_number'))
                ->required(),

            Toggle::make('is_active')
                ->label(__('transaction.bank_is_active'))
                ->default(true),
        ];
    }
}
