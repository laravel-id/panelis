<?php

namespace App\Filament\Resources\Transaction;

use App\Filament\Resources\Transaction\BankResource\Pages;
use App\Models\Transaction\Bank;
use App\Services\Payments\Vendor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.event');
    }

    public static function getLabel(): ?string
    {
        return __('transaction.bank');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor')
                    ->label(__('transaction.bank_vendor'))
                    ->formatStateUsing(function (?string $state): string {
                        $vendor = Vendor::tryFrom($state);
                        if (empty($vendor)) {
                            return __('Not Found');
                        }

                        return $vendor->label();
                    }),

                TextColumn::make('bank_name')
                    ->label(__('transaction.bank_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_name')
                    ->label(__('transaction.bank_account_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_number')
                    ->label(__('transaction.bank_account_number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('balance')
                    ->label(__('transaction.bank_balance'))
                    ->alignEnd()
                    ->formatStateUsing(fn (float $state): string => Number::money($state)),

                TextColumn::make('local_created_at')
                    ->label(__('ui.created_at'))
                    ->dateTime(get_datetime_format()),

            ])
            ->filters([
                //
            ])
            ->actions([

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
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
}
