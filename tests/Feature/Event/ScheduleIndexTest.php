<?php

namespace Tests\Feature\Event;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleIndexTest extends TestCase
{
    public function test_index(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSeeText(config('app.name'));
    }
}
