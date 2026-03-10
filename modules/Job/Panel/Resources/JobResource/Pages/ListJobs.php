<?php

namespace Modules\Job\Panel\Resources\JobResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Job\Panel\Resources\JobResource;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
