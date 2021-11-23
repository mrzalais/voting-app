<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IdeaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_if_idea_is_voted_for_by_user()
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

        $this->assertTrue($idea->isVotedByUser($userA));
        $this->assertFalse($idea->isVotedByUser($userB));
        $this->assertFalse($idea->isVotedByUser(null));
    }
}
