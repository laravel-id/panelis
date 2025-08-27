<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchUpdated;
use App\Filament\Resources\BranchResource\Forms\BranchForm;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;

class EditBranch extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('branch.edit');
    }

    public function form(Form $form): Form
    {
        return $form->schema(BranchForm::schema());
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
