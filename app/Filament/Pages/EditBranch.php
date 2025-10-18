<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchUpdated;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
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
        return $schema->components([
            Section::make([
                TextInput::make('name')
                    ->label(__('branch.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
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
                    ->placeholder('business@email-example.com')
                    ->email(),

                Textarea::make('address')
                    ->label(__('branch.address'))
                    ->rows(5)
                    ->nullable(),
            ]),
        ]);
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
