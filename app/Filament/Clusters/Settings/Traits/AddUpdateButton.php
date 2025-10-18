<?php

namespace App\Filament\Clusters\Settings\Traits;

use Filament\Actions\Action;

trait AddUpdateButton
{
    public bool $isButtonDisabled = true;

    protected function getUpdateAction(): Action
    {
        return Action::make('update_setting')
            ->label(__('ui.btn.update'))
            ->color('primary')
            ->disabled($this->isButtonDisabled)
            ->action('update');
    }
}
