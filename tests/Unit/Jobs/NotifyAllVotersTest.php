<?php

namespace Tests\Unit\Jobs;

use App\Jobs\NotifyAllVoters;
use App\Mail\IdeaStatusUpdatedMailable;
use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotifyAllVotersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_an_email_to_all_voters()
    {
        $admin = User::factory()->create([
            'email' => 'jon@doe.com',
        ]);

        $nonAdmin = User::factory()->create([
            'email' => 'user@user.com',
        ]);
        
        $userB = User::factory()->create();

        $category = Category::factory()->create(['name' => 'Category 1']);

        $status = Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $admin->id,
            'title' => 'My First Idea',
            'category_id' => $category->id,
            'status_id' => $status->id,
            'description' => 'Description of my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $admin->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $nonAdmin->id,
        ]);
        
        Mail::fake();

        NotifyAllVoters::dispatch($idea);
        
        Mail::assertQueued(IdeaStatusUpdatedMailable::class, function($mail) {
            return $mail->hasTo('jon@doe.com')
                && $mail->build()->subject === 'An idea you voted for has a new status';
        });

        Mail::assertQueued(IdeaStatusUpdatedMailable::class, function($mail) {
            return $mail->hasTo('user@user.com')
                && $mail->build()->subject === 'An idea you voted for has a new status';
        });
    }
}
