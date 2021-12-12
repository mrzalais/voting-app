<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;

class IdeaComment extends Component
{
    public Comment $comment;
    public int $ideaUserId;

    public function mount(Comment $comment, int $ideaUserId): void
    {
        $this->comment = $comment;
        $this->ideaUserId = $ideaUserId;
    }


    public function render()
    {
        return view('livewire.idea-comment');
    }
}
