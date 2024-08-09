<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    public function test_example(): void
    {
        $response = $this->get('/contact');

        $response->assertSuccessful()
            ->assertViewIs('pages.messages.form');
    }
}
