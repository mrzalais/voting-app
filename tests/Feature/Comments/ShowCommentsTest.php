<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Idea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function idea_comments_livewire_component_renders(): void
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment'
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comments');
    }

    /** @test */
    public function idea_comment_livewire_component_renders(): void
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment'
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comment');
    }

    /** @test */
    public function no_comments_show_appropriate_message(): void
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertSee('No comments yet');
    }

    /** @test */
    public function list_of_comments_shows_on_idea_page(): void
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment'
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'Second comment'
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeInOrder(['First comment', 'Second comment'])
            ->assertSee('2 comments');
    }

    /** @test */
    public function comment_count_shows_correctly_on_index_page(): void
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment'
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'Second comment'
        ]);

        $this->get(route('idea.index', $idea))
            ->assertSee('2 comments');
    }

    /** @test */
    public function op_badge_shows_if_author_of_idea_comments_on_idea(): void
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment'
        ]);
        Comment::factory()->create([
            'user_id' => $idea->user->id,
            'idea_id' => $idea->id,
            'body' => 'Second comment'
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSee('OP');
    }

    /** @test */
    public function comments_pagination_works(): void
    {
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea
        ]);

        Comment::factory($comment->getPerPage())->create([
            'idea_id' => $idea->id,
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSee($comment->body)
            ->assertDontSee(Comment::find(Comment::count())->body);

        $this->get(route('idea.show', [
            'idea' => $idea,
            'page' => 2,
        ]))
        ->assertDontSee($comment->body)
        ->assertSee(Comment::find(Comment::count())->body);
    }
}
