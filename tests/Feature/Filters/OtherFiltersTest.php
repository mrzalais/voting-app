<?php

namespace Tests\Feature\Filters;

use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Livewire\Livewire;
use App\Models\Category;
use App\Http\Livewire\IdeasIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtherFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function top_voted_filter_works()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();

        $ideaOne = Idea::factory()->create([
            'user_id' => $userA->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $userB->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaOne->id,
            'user_id' => $userA->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaOne->id,
            'user_id' => $userB->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaTwo->id,
            'user_id' => $userC->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'Top Voted')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->votes()->count() === 2
                    && $ideas->get(1)->votes()->count() === 1;
            });
    }

    /** @test */
    public function my_ideas_filter_works_correctly_when_user_logged_in()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Idea::factory()->create([
            'user_id' => $userA->id,
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $userA->id,
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $userB->id,
            'title' => 'Third Idea',
        ]);

        Livewire::actingAs($userA)
            ->test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->title === 'Second Idea'
                    && $ideas->get(1)->title === 'First Idea';
            });
    }

    /** @test */
    public function my_ideas_filter_works_correctly_when_user_is_not_logged_in()
    {
        Idea::factory()->count(3)->create();

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function my_ideas_filter_works_correctly_with_categories_filter()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'Third Idea',
        ]);

        Livewire::actingAs($user)
            ->test(IdeasIndex::class)
            ->set('category', 'Category 1')
            ->set('filter', 'My Ideas')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->title === 'Second Idea'
                    && $ideas->get(1)->title === 'First Idea';
            });
    }

    /** @test */
    public function no_filters_works_correctly()
    {
        $user = User::factory()->create();

        Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'user_id' => $user->id,
            'title' => 'Third Idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'No Filter')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3
                    && $ideas->first()->title === 'Third Idea'
                    && $ideas->get(1)->title === 'Second Idea';
            });
    }
}
