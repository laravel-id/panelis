<?php

namespace App\Filament\Resources\TranslationResource\Enums;

enum TranslationPermission: string
{
    case Browse = 'BrowseTranslation';

    case Read = 'ReadTranslation';

    case Edit = 'EditTranslation';

    case Add = 'AddTranslation';

    case Delete = 'DeleteTranslation';

    case Import = 'ImportTranslation';

    case Export = 'ExportTranslation';

    case Backup = 'BackupTranslation';

    case Restore = 'RestoreTranslation';
}
