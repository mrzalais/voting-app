<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class AddComment extends Component
{
    public Idea $idea;
    public string $comment = '';
    protected array $rules = [
        'comment' => 'required|min:4',
    ];

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function addComment()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->validate();

        Comment::create([
            'user_id' => auth()->id(),
            'idea_id' => $this->idea->id,
            'body' => $this->comment,
        ]);

        $this->reset('comment');

        $this->emit('commentWasAdded', 'Comment was posted!');
    }

    public function render()
    {
        return view('livewire.add-comment');
    }
}
