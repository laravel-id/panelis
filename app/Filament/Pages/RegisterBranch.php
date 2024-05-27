<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchRegistered;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterBranch extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('branch.register');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('branch.name'))
                ->maxLength(100)
                ->unique()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    $set('slug', Str::slug($state));
                }),

            TextInput::make('slug')
                ->label(__('branch.alias')),

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
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $model = $this->getModel()::create($data);
        $model->users()->attach(['user_id' => Auth::id()]);

        event(new BranchRegistered($model));

        return $model;
    }
}
