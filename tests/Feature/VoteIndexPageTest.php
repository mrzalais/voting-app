<?php

namespace Tests\Feature;

use App\Http\Livewire\IdeaIndex;
use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use App\Models\Status;
use Livewire\Livewire;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteIndexPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_page_contains_idea_index_livewire_component()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        $this->get(route('idea.index'))
            ->assertSeeLivewire('idea-index');
    }

    /** @test */
    public function index_page_correctly_receives_vote_count()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $userA->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $userA->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $userB->id,
        ]);

        $this->get(route('idea.index'))
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->first()->votes_count == 2;
            });
    }

    /** @test */
    public function vote_count_shows_correctly_on_index_page_livewire_component()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        Livewire::test(IdeaIndex::class, [
            'idea' => $idea,
            'voteCount' => 5,
        ])
            ->assertSet('voteCount', 5);
    }

    /** @test */
    public function user_who_is_logged_in_sees_voted_if_idea_already_voted_for()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get(route('idea.index'));

        $ideaWithVotes = $response['ideas']->items()[0];

        Livewire::actingAs($user)
            ->test(IdeaIndex::class, [
                'idea' => $ideaWithVotes,
                'voteCount' => 5,
            ])
            ->assertSet('hasVoted', true)
            ->assertSee('Voted');
    }
}
