<?php

namespace Modules\Branch\Panel\Resources\BranchResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Branch\Panel\Resources\BranchResource;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\DeleteAction::make()
                ->visible(user_can(BranchResource\Enums\BranchPermission::Delete)),
        ];
    }
}
