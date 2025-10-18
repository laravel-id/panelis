<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchUpdated;
use App\Filament\Resources\BranchResource\Forms\BranchForm;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditBranch extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('branch.edit');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema(BranchForm::schema());
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        event(new BranchUpdated($record));

        return $record;
    }

    public function getRedirectUrl(): ?string
    {
        return EditBranch::getUrl();
    }
}
