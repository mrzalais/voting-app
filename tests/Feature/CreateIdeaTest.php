<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;
use App\Models\User;
use App\Models\Status;
use Livewire\Livewire;
use App\Models\Category;
use App\Http\Livewire\CreateIdea;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateIdeaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_idea_form_does_not_show_when_logged_out(): void
    {
        $response = $this->get(route('idea.index'));

        $response->assertSuccessful();
        $response->assertSee('Please log in to create an idea.');
        $response->assertDontSee('Let us know what you would like, and we\'ll take a look over!', false);
    }

    /** @test */
    public function create_idea_form_does_show_when_logged_in(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('idea.index'));

        $response->assertSuccessful();
        $response->assertDontSee('Please log in to create an idea.');
        $response->assertSee('Let us know what you would like, and we\'ll take a look over!', false);
    }

    /** @test */
    public function main_page_contains_create_idea_livewire_component(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('idea.index'))
            ->assertSeeLivewire('create-idea');
    }

    /** @test */
    public function create_idea_form_validation_works(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CreateIdea::class)
            ->set('title', '')
            ->set('category', 1)
            ->set('description', '')
            ->call('createIdea')
            ->assertHasErrors(['title', 'category', 'description'])
            ->assertSee('The title field is required');
    }

    /** @test */
    public function creating_an_idea_works_correctly(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        Status::factory()->create(['name' => 'Open']);

        Livewire::actingAs($user)
            ->test(CreateIdea::class)
            ->set('title', 'My First Idea')
            ->set('category', $categoryOne->id)
            ->set('description', 'This is my first idea')
            ->call('createIdea')
            ->assertRedirect('/');

        $response = $this->actingAs($user)->get(route('idea.index'));
        $response->assertSuccessful();
        $response->assertSee('My First Idea');
        $response->assertSee('This is my first idea');

        $this->assertDatabaseHas('ideas', [
            'title' => 'My First Idea',
        ]);

        $this->assertDatabaseHas('votes', [
            'idea_id' => 1,
            'user_id' => 1,
        ]);
    }

    /** @test */
    public function creating_two_ideas_with_same_title_still_works_but_has_different_slugs(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        Status::factory()->create(['name' => 'Open']);

        Livewire::actingAs($user)
            ->test(CreateIdea::class)
            ->set('title', 'My First Idea')
            ->set('category', $categoryOne->id)
            ->set('description', 'This is my first idea')
            ->call('createIdea')
            ->assertRedirect('/');

        $this->assertDatabaseHas('ideas', [
            'title' => 'My First Idea',
            'slug' => 'my-first-idea',
        ]);

        Livewire::actingAs($user)
            ->test(CreateIdea::class)
            ->set('title', 'My First Idea')
            ->set('category', $categoryOne->id)
            ->set('description', 'This is my first idea')
            ->call('createIdea')
            ->assertRedirect('/');

        $this->assertDatabaseHas('ideas', [
            'title' => 'My First Idea',
            'slug' => 'my-first-idea-2',
        ]);
    }
}
