<?php

namespace Modules\Module\Panel\Resources\Resources\ModuleResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Modules\Module\Panel\Resources\ModuleResource;
use Modules\Module\Panel\Resources\Resources\ModuleResource\Enums\ModulePermission;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label(__('module::module.btn.sync'))
                ->action(function (): void {
                    Notification::make()
                        ->title(__('module::module.notifications.synced'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function mount(): void
    {
        abort_unless(user_can(ModulePermission::Browse), Response::HTTP_FORBIDDEN);
    }
}
