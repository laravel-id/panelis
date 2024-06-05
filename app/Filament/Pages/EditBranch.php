<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchUpdated;
use App\Models\Branch;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
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
            Section::make([
                TextInput::make('name')
                    ->label(__('branch.name'))
                    ->required()
                    ->maxLength(100),

                TextInput::make('slug')
                    ->alphaDash()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('phone')
                    ->label(__('branch.phone'))
                    ->nullable()
                    ->tel(),

                TextInput::make('email')
                    ->label(__('branch.email'))
                    ->nullable()
                    ->email(),

                Textarea::make('address')
                    ->label(__('branch.address'))
                    ->rows(5)
                    ->nullable(),
            ]),
        ]);
    }

    public function handleRecordUpdate(Model $model, array $data): Branch
    {
        $data['slug'] = Str::slug($data['name']);
        $model->update($data);

        event(new BranchUpdated($model));

        return $model;
    }

    public function getRedirectUrl(): ?string
    {
        return EditBranch::getUrl();
    }
}
