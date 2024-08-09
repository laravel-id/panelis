<?php

namespace App\Http\Controllers;

use App\Models\Changelog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChangelogController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_if(!config('app.demo', false) && !app()->isLocal(), 404);

        $changelogs = Changelog::query()
            ->orderByDesc('logged_at')
            ->get();

        return view('changelogs.index')
            ->with('changelogs', $changelogs)
            ->with('title', __('changelog.changelog'));
    }
}
