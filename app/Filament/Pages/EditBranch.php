<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditBranch extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('branch.edit');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('branch.name'))
                ->maxLength(100),
            // ->unique(),
        ]);
    }

    public function handleRecordUpdate(Model $model, array $data): Branch
    {
        $data['slug'] = Str::slug($data['name']);
        $model->update($data);

        return $model;
    }

    public function getRedirectUrl(): ?string
    {
        return '/admin';
    }
}
