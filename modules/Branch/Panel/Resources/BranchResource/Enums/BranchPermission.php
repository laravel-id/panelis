<?php

namespace Modules\Branch\Panel\Resources\BranchResource\Enums;

enum BranchPermission: string
{
    case Browse = 'BrowseBranch';

    case Edit = 'EditBranch';

    case Delete = 'DeleteBranch';

    case Create = 'CreateBranch';
}
