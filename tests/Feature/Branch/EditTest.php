<?php

use App\Filament\Pages\EditBranch;
use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->branch = Branch::factory()
        ->for($this->user)
        ->create();

    $this->user->branches()->attach($this->branch);

    actingAs($this->user);
    Filament::setTenant($this->branch);
});

it('does exist', function (): void {
    $response = $this->get(url('admin', $this->branch->slug, 'profile'));

    $response->assertOk();
});

it('validates input with existing data', function (): void {
    livewire(EditBranch::class)
        ->fillForm([
            'name' => null,
            'slug' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'name' => ['required'],
            'slug' => ['required'],
        ]);
});

it('validates unique name and slug input', function (): void {
    $branch = Branch::factory()
        ->for($this->user)
        ->create();

    livewire(EditBranch::class)
        ->fillForm([
            'name' => $branch->name,
            'slug' => $branch->slug,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'name' => ['unique'],
            'slug' => ['unique'],
        ]);
});

it('validates phone number input', function (): void {
    livewire(EditBranch::class)
        ->fillForm([
            'name' => $this->branch->name,
            'slug' => $this->branch->slug,
            'phone' => 'invalid-phone',
        ])
        ->call('save')
        ->assertHasFormErrors([
            'phone' => ['regex'],
        ]);
});

it('updates existing branch data', function (): void {
    $name = fake()->unique()->company;

    livewire(EditBranch::class)
        ->fillForm([
            'name' => $name,
            'slug' => Str::slug($name),
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertOk();

    $this->assertDatabaseHas((new Branch)->getTable(), [
        'id' => Filament::getTenant()->id,
        'name' => $name,
        'slug' => Str::slug($name),
    ]);
});
