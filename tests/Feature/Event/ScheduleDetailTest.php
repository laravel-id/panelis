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

        $response->assertStatus(200)
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

        $response->assertStatus(404);
    }

    public function test_related_schedules_data(): void
    {
        $schedule = Schedule::factory()->create();
        $response = $this->get('/event/'.$schedule->slug);

        $response->assertStatus(200)
            ->assertViewHas('relatedSchedules');
    }

    public function test_calendar_link_data(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->get('/event/'.$schedule->slug);

        $response->assertViewHas('calendar');
    }
}
