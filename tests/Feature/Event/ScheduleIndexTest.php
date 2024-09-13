<?php

namespace Tests\Feature\Event;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleIndexTest extends TestCase
{
    public function test_index(): void
    {
        $this->markTestSkipped('Virtual table \'events\' not found.');

        $response = $this->get('/');

        $response->assertSuccessful()
            ->assertViewIs('pages.schedules.index')
            ->assertSeeText(config('app.name'));
    }
}
