<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use Livewire\Livewire;
use App\Http\Livewire\SetStatus;
use App\Jobs\NotifyAllVoters;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminSetStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function show_page_contains_set_status_livewire_component_when_user_is_admin(): void
    {
        /** @var Authenticatable $admin */
        $admin = User::factory()->admin()->create();

        $idea = Idea::factory()->create();

        $this->actingAs($admin)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('set-status');
    }

    /** @test */
    public function show_page_does_not_contain_set_status_livewire_component_when_user_is_not_an_admin(): void
    {
        /** @var Authenticatable $nonAdmin */
        $nonAdmin = User::factory()->create();

        $idea = Idea::factory()->create();

        $this->actingAs($nonAdmin)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('set-status');
    }

    /** @test */
    public function initial_status_is_set_correctly(): void
    {
        /** @var Authenticatable $admin */
        $admin = User::factory()->admin()->create();

        $status = Status::factory()->create();

        $idea = Idea::factory()->create([
            'status_id' => $status->id,
        ]);

        Livewire::actingAs($admin)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->assertSet('status', $status->id);
    }

    /** @test */
    public function can_set_status_correctly_no_comment(): void
    {
        /** @var Authenticatable $admin */
        $admin = User::factory()->admin()->create();

        $statusOpen = Status::factory()->create(['name' => 'Open']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering']);

        $idea = Idea::factory()->create([
            'status_id' => $statusOpen->id,
        ]);

        Livewire::actingAs($admin)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->set('status', $statusConsidering->id)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdated');

        $this->assertDatabaseHas('ideas', [
            'id' => $idea->id,
            'status_id' => $statusConsidering->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'No comment was added',
            'is_status_update' => true,
        ]);
    }

    /** @test */
    public function can_set_status_correctly_with_comment(): void
    {
        /** @var Authenticatable $admin */
        $admin = User::factory()->admin()->create();

        $statusOpen = Status::factory()->create(['name' => 'Open']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering']);

        $idea = Idea::factory()->create([
            'status_id' => $statusOpen->id,
        ]);

        Livewire::actingAs($admin)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->set('status', $statusConsidering->id)
            ->set('comment', 'Set status comment')
            ->call('setStatus')
            ->assertEmitted('statusWasUpdated');

        $this->assertDatabaseHas('ideas', [
            'id' => $idea->id,
            'status_id' => $statusConsidering->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'Set status comment',
            'is_status_update' => true,
        ]);
    }

    /** @test */
    public function can_set_status_correctly_while_notifying_all_voters(): void
    {
        /** @var Authenticatable $admin */
        $admin = User::factory()->admin()->create();

        $statusOpen = Status::factory()->create(['name' => 'Open']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering']);

        $idea = Idea::factory()->create([
            'status_id' => $statusOpen->id,
        ]);

        Queue::fake();

        Queue::assertNothingPushed();

        Livewire::actingAs($admin)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->set('status', $statusConsidering->id)
            ->set('notifyAllVoters', true)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdated');

        Queue::assertPushed(NotifyAllVoters::class);
    }
}
