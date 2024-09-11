<?php

namespace App\Console\Commands\Event;

use App\Models\Event\Event;
use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use App\Models\Event\Type;
use Illuminate\Console\Command;

class IndexScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all registered schedule into events virtual table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $schedules = Schedule::query()
            ->orderBy('id')
            ->with([
                'organizers',
                'types',
                'district.region',
            ])
            ->get();

        $this->withProgressBar($schedules, function (Schedule $schedule): void {
            Event::query()->updateOrCreate(['id' => $schedule->id], array_merge(
                $schedule->only([
                    'id',
                    'slug',
                    'title',
                    'description',
                    'categories',
                    'started_at',
                    'finished_at',
                    'is_virtual',
                ]),
                [
                    'location' => $schedule->full_location,
                    'region' => $schedule->district?->region?->name,
                    'types' => $schedule->types->map(fn (Type $type): ?string => $type->title),
                    'organizers' => $schedule->organizers->map(fn (Organizer $organizer): ?string => $organizer->name),
                    'is_pinned' => $schedule->metadata['is_pinned'] ?? false,
                ],
            ));
        });

        return self::SUCCESS;
    }
}
