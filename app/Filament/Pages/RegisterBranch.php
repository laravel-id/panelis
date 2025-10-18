<?php

namespace App\Filament\Pages;

use App\Events\Branch\BranchRegistered;
use App\Filament\Resources\BranchResource\Forms\BranchForm;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RegisterBranch extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('branch.register');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema(BranchForm::schema());
    }

    protected function handleRegistration(array $data): Model
    {
        $data['user_id'] = Auth::id();
        $model = $this->getModel()::create($data);
        $model->users()->attach(['user_id' => Auth::id()]);

        event(new BranchRegistered($model));

        return $model;
    }

    public static function getUrl(): string
    {
        return Filament::getPanel('admin')->getTenantRegistrationUrl();
    }
}
