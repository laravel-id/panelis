<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Actions\User\BackupPermission;
use App\Filament\Resources\PermissionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(Auth::user()->can('Create permission'))
                ->mutateFormDataUsing(function (array $data): array {
                    $key = Str::snake($data['name']);

                    $data['label'] = "user.permission_label_{$key}";
                    $data['description'] = "user.permission_description_{$key}";

                    return $data;
                }),

            Action::make('backup_permission')
                ->label(__('user.btn_backup_permission'))
                ->requiresConfirmation()
                ->action(function (): void {
                    $path = BackupPermission::run();

                    Notification::make('permission_stored')
                        ->title(__('user.permission_backed_up'))
                        ->body(__('user.permission_backed_up_body', ['path' => $path]))
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('View user'), 403);
    }
}
