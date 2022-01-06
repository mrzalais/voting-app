<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Http\Response;
use App\Http\Livewire\IdeaShow;
use App\Http\Livewire\IdeaIndex;
use App\Http\Livewire\MarkIdeaAsSpam;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_mark_idea_as_spam_livewire_component_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        /** @var Authenticatable $user */
        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('mark-idea-as-spam');
    }

    /** @test */
    public function does_not_show_mark_idea_as_spam_livewire_component_when_user_does_not_have_authorization(): void
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('mark-idea-as-spam');
    }

    /** @test */
    public function marking_an_idea_as_spam_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        /** @var Authenticatable $user */
        Livewire::actingAs($user)
            ->test(MarkIdeaAsSpam::class, [
                'idea' => $idea,
            ])
            ->call('markAsSpam')
            ->assertEmitted('ideaWasMarkedAsSpam');

        $this->assertEquals(1, Idea::first()->spam_reports);
    }

    /** @test */
    public function marking_an_idea_as_spam_does_not_work_when_user_does_not_have_authorization(): void
    {
        $idea = Idea::factory()->create();

        /** @var Authenticatable $user */
        Livewire::test(MarkIdeaAsSpam::class, [
            'idea' => $idea,
        ])
            ->call('markAsSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function marking_an_idea_as_spam_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'voteCount' => 4,
            ])
            ->assertSee('Mark as Spam');
    }

    /** @test */
    public function marking_an_idea_as_spam_does_not_show_on_menu_when_user_does_not_have_authorization(): void
    {
        $idea = Idea::factory()->create();

        Livewire::test(IdeaShow::class, [
            'idea' => $idea,
            'voteCount' => 4,
        ])
            ->assertDontSee('Mark as Spam');
    }

    /** @test */
    public function spam_reports_count_shows_on_ideas_index_page_if_logged_in_as_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $idea = Idea::factory()->create([
            'spam_reports' => 3,
        ]);

        Livewire::actingAs($admin)
            ->test(IdeaIndex::class, [
                'idea' => $idea,
                'voteCount' => 4,
            ])
            ->assertSee('Spam reports: 3');
    }

    /** @test */
    public function spam_reports_count_shows_on_idea_show_page_if_logged_in_as_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $idea = Idea::factory()->create([
            'spam_reports' => 3,
        ]);

        Livewire::actingAs($admin)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'voteCount' => 4,
            ])
            ->assertSee('Spam reports: 3');
    }
}
