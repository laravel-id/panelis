<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
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
                ->unique(),
        ]);
    }

    public function handleRegistration(array $data): Branch
    {
        $branch = Branch::create([
            'user_id' => Auth::id(),
            'slug' => Str::slug($data['name']),
            'name' => $data['name'],
        ]);
        $branch->users()->attach(['user_id' => Auth::id()]);

        return $branch;
    }
}
