<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Sitemap::create()
            ->add([
                route('schedule.archive'),
                route('message.form'),
            ])
            ->add(Organizer::all())
            ->add(Schedule::getPublishedSchedules())
            ->toResponse($request);
    }
}
