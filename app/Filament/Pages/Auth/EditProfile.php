<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class EditProfile extends \Filament\Pages\Auth\EditProfile
{
    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),

            Section::make(__('Profile'))
                ->relationship('profile')
                ->schema([
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(15),
                ]),
        ]);
    }
}
