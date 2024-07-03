<?php

it('returns index page', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSeeText(config('app.name'));
});
