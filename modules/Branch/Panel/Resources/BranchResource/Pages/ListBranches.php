<?php

namespace Modules\Branch\Panel\Resources\BranchResource\Pages;

use App\Filament\Pages\RegisterBranch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Branch\Panel\Resources\BranchResource;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(RegisterBranch::getUrl())
                ->visible(user_can(BranchResource\Enums\BranchPermission::Create)),
        ];
    }
}
