<?php

namespace Tests\Feature\Comments;

use App\Http\Livewire\DeleteComment;
use App\Http\Livewire\IdeaComment;
use App\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_delete_comment_livewire_component_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Authenticatable $user */
        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('delete-comment');
    }

    /** @test */
    public function does_not_show_edit_comment_livewire_component_when_user_does_not_have_authorization(): void
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('delete-comment');
    }

    /** @test */
    public function delete_comment_is_set_correctly_when_user_clicks_it_from_menu(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'First comment',
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->assertEmitted('deleteCommentWasSet');
    }

    /** @test */
    public function deleting_a_comment_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'First comment',
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment')
            ->assertEmitted('commentWasDeleted');

        $this->assertEquals(0, Comment::count());
    }

    /** @test */
    public function user_cannot_delete_other_user_comment_without_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment',
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function deleting_a_comment_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'First comment',
        ]);

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertSee('Delete Comment');
    }

    /** @test */
    public function deleting_a_comment_does_not_show_on_menu_when_user_does_not_have_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'First comment',
        ]);

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertDontSee('Delete Comment');
    }
}
