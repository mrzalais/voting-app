<?php

namespace Tests\Unit;

use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_count_of_each_status(): void
    {
        $statuses = [
            'Open' => 15,
            'Considering' => 10,
            'In Progress' => 5,
            'Implemented' => 1,
            'Closed' => 4,
        ];

        $totalCount = 0;
        foreach ($statuses as $status => $count) {
            Idea::factory()
                ->for(User::factory()->create())
                ->for(Category::factory()->create())
                ->forStatus(['name' => $status])
                ->count($count)
                ->create();

            $totalCount += $count;
        };
        $this->assertEquals($totalCount, Status::getCount()['all_statuses']);
        $this->assertEquals(15, Status::getCount()['open']);
        $this->assertEquals(10, Status::getCount()['considering']);
        $this->assertEquals(5, Status::getCount()['in_progress']);
        $this->assertEquals(1, Status::getCount()['implemented']);
        $this->assertEquals(4, Status::getCount()['closed']);
    }
}
