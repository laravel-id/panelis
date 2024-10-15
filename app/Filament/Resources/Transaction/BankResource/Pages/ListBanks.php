<?php

namespace App\Filament\Resources\Transaction\BankResource\Pages;

use App\Actions\Transaction\FetchBank;
use App\Filament\Resources\Transaction\BankResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListBanks extends ListRecords
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('transaction.btn_refresh_bank'))
                ->action(function (): void {
                    FetchBank::run();

                    $this->resetTable();
                }),
        ];
    }
}
