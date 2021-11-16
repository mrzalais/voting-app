<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowIdeasTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function list_of_ideas_shows_on_main_page()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'class' => 'bg-purple text-white']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'description' => 'Description of my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My Second Idea',
            'category_id' => $categoryTwo->id,
            'status_id' => $statusConsidering->id,
            'description' => 'Description of my Second idea',
        ]);

        $response = $this->get(route('idea.index'));

        $response->assertSuccessful();
        $response->assertSee($ideaOne->title);
        $response->assertSee($ideaOne->description);
        $response->assertSee($categoryOne->name);
        $response->assertSee($ideaTwo->title);
        $response->assertSee($ideaTwo->description);
        $response->assertSee($categoryTwo->name);
    }

    /** @test */
    public function single_idea_shows_correctly_on_the_show_page()
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

        $response = $this->get(route('idea.show', $idea));

        $response->assertSuccessful();
        $response->assertSee($idea->title);
        $response->assertSee($idea->description);
        $response->assertSee($category->name);
    }

    /** @test */
    public function ideas_pagination_works()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);
        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        Idea::factory(Idea::PAGINATION_COUNT + 1)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
        ]);

        $ideaOnFirstPage = Idea::find(1);
        $ideaOnFirstPage->title = 'My Idea On First Page';
        $ideaOnFirstPage->save();
        $ideaOnSecondPage = Idea::find(Idea::PAGINATION_COUNT + 1);
        $ideaOnSecondPage->title = 'My Idea On Second Page';
        $ideaOnSecondPage->save();

        $response = $this->get('/');

        $response->assertSee($ideaOnSecondPage->title);
        $response->assertDontSee($ideaOnFirstPage->title);

        $response = $this->get('/?page=2');

        $response->assertSee($ideaOnFirstPage->title);
        $response->assertDontSee($ideaOnSecondPage->title);
    }

    /** @test */
    public function same_idea_title_different_slugs()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);
        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,

            'description' => 'Another Description of my first idea',
        ]);

        $response = $this->get(route('idea.show', $ideaOne));

        $response->assertSuccessful();
        $this->assertTrue(request()->path() === 'ideas/my-first-idea');

        $response = $this->get(route('idea.show', $ideaTwo));

        $response->assertSuccessful();
        $this->assertTrue(request()->path() === 'ideas/my-first-idea-2');
    }
}
