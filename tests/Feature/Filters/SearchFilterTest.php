<?php

namespace Tests\Feature\Filters;

use Tests\TestCase;
use App\Models\Idea;
use Livewire\Livewire;
use App\Models\Category;
use App\Http\Livewire\IdeasIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function searching_works_when_string_length_is_more_than_3_characters(): void
    {
        Idea::factory()->create([
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'title' => 'Third Idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('search', 'Second')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 1
                    && $ideas->first()->title === 'Second Idea';
            });
    }

    /** @test */
    public function searching_does_not_work_when_string_length_is_less_than_3_characters(): void
    {
        Idea::factory()->create([
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'title' => 'Third Idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('search', 'Se')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3;
            });
    }

    /** @test */
    public function search_works_correctly_with_category_filters(): void
    {
        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        Idea::factory()->create([
            'category_id' => $categoryOne->id,
            'title' => 'First Idea',
        ]);

        Idea::factory()->create([
            'category_id' => $categoryOne->id,
            'title' => 'Second Idea',
        ]);

        Idea::factory()->create([
            'category_id' => $categoryTwo->id,
            'title' => 'Third Idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Category 1')
            ->set('search', 'Idea')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2;
            });
    }
}
