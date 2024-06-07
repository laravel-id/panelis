<?php

use App\Events\Branch\BranchRegistered;
use App\Filament\Pages\RegisterBranch;
use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('does exist', function (): void {
    $response = $this->get(url(Filament::getCurrentPanel()->getPath(), RegisterBranch::getSlug()));

    $response->assertStatus(200);
});

it('validates input', function () {
    livewire(RegisterBranch::class)
        ->fillForm([
            'name' => null,
            'slug' => null,
        ])
        ->assertFormExists()
        ->call('register')
        ->assertHasFormErrors([
            'name' => ['required'],
            'slug' => ['required'],
        ]);
});

it('validates unique name and slug', function (): void {
    $branch = Branch::factory()
        ->for($this->user)
        ->create();

    livewire(RegisterBranch::class)
        ->fillForm([
            'name' => $branch->name,
            'slug' => $branch->slug,
        ])
        ->call('register')
        ->assertHasFormErrors([
            'name' => ['unique'],
            'slug' => ['unique'],
        ]);
});

it('validates phone number input', function (): void {
    $branch = Branch::factory()->make();

    livewire(RegisterBranch::class)
        ->fillForm([
            'name' => $branch->name,
            'slug' => Str::slug($branch->name),
            'phone' => 'invalid-phone',
        ])
        ->call('register')
        ->assertHasFormErrors([
            'phone' => ['regex'],
        ]);
});

it('creates slug automatically from name', function (): void {
    $branch = Branch::factory()->make();

    livewire(RegisterBranch::class)
        ->fillForm([
            'name' => $branch->name,
        ])
        ->assertFormSet([
            'slug' => Str::slug($branch->name),
        ]);
});

it('creates a new branch successfully', function () {
    Event::fake();

    $branch = Branch::factory()->make();

    livewire(RegisterBranch::class)
        ->fillForm([
            'name' => $branch->name,
            'slug' => $slug = Str::slug($branch->name),
        ])
        ->call('register')
        ->assertHasNoFormErrors()
        ->assertOk();

    $this->assertDatabaseHas((new Branch)->getTable(), [
        'name' => $branch->name,
        'slug' => $slug,
    ]);

    $this->assertDatabaseHas('branch_user', [
        'user_id' => $this->user->id,
        'branch_id' => Branch::whereSlug($slug)->first()?->id,
    ]);

    Event::dispatched(BranchRegistered::class);
});
