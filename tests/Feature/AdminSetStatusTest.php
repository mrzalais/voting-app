<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use Livewire\Livewire;
use App\Models\Category;
use App\Http\Livewire\SetStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminSetStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function show_page_contains_set_status_livewire_component_when_user_is_admin()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create([
            'email' => 'jon@doe.com',
        ]);

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
        ]);

        $this->actingAs($admin)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('set-status');
    }

    /** @test */
    public function show_page_does_not_contain_set_status_livewire_component_when_user_is_not_an_admin()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $nonAdmin */
        $nonAdmin = User::factory()->create([
            'email' => 'user@user.com',
        ]);

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $nonAdmin->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
        ]);

        $this->actingAs($nonAdmin)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('set-status');
    }

    /** @test */
    public function initial_status_is_set_correctly()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create([
            'email' => 'jon@doe.com',
        ]);

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
        ]);

        Livewire::actingAs($admin)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->assertSet('status', $status->id);
    }

    /** @test */
    public function can_set_status_correctly()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create([
            'email' => 'jon@doe.com',
        ]);

        $category = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
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
    }
}
