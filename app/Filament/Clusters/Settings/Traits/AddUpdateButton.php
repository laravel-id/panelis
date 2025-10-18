<?php

namespace App\Filament\Clusters\Settings\Traits;

use Filament\Actions\Action;

trait AddUpdateButton
{
    protected function getUpdateAction(): Action
    {
        return Action::make('update_setting')
            ->label(__('ui.btn.update'))
            ->color('primary')
            ->action('update');
    }
}
