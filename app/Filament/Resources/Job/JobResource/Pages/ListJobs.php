<?php

namespace app\Filament\Resources\Job\JobResource\Pages;

use app\Filament\Resources\Job\JobResource;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
