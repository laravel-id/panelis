<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchRegistered;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterBranch extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('branch.register');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('branch.name'))
                ->maxLength(50)
                ->minLength(3)
                ->required()
                ->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    $set('slug', Str::slug($state));
                }),

            TextInput::make('slug')
                ->label(__('branch.alias'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(60)
                ->minLength(3)
                ->alphaDash(),

            TextInput::make('phone')
                ->label(__('branch.phone'))
                ->nullable()
                ->tel(),

            TextInput::make('email')
                ->label(__('branch.email'))
                ->placeholder('mail@business.com')
                ->nullable()
                ->email(),

            Textarea::make('address')
                ->label(__('branch.address'))
                ->rows(5)
                ->nullable(),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $data['user_id'] = Auth::id();
        $model = $this->getModel()::create($data);
        $model->users()->attach(['user_id' => Auth::id()]);

        event(new BranchRegistered($model));

        return $model;
    }
}
