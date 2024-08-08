<?php

namespace Tests\Feature\Event;

use App\Models\Event\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_detail(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->get('/event/'.$schedule->slug);

        $response->assertSuccessful()
            ->assertViewIs('pages.schedules.view')
            ->assertViewHas([
                'timezone',
                'schedule',
                'organizers',
                'externalUrl',
                'title',
                'startedAt',
            ])
            ->assertSeeText($schedule->title)
            ->assertSeeText($schedule->description)
            ->assertSeeText($schedule->started_at->timezone(get_timezone())->format(get_date_format()));
    }

    public function test_schedule_not_found(): void
    {
        $response = $this->get('/event/not-found');

        $response->assertNotFound();
    }

    public function test_related_schedules_data(): void
    {
        $schedule = Schedule::factory()->create();
        $response = $this->get('/event/'.$schedule->slug);

        $response->assertSuccessful()
            ->assertViewHas('relatedSchedules');
    }

    public function test_calendar_link_data(): void
    {
        $schedule = Schedule::factory()->create([
            'started_at' => now()->addMonth(),
            'finished_at' => null,
        ]);

        $response = $this->get('/event/'.$schedule->slug);

        $response->assertSuccessful()
            ->assertViewHas('calendar')
            ->assertSeeText(__('event.add_to_calendar'));
    }
}
