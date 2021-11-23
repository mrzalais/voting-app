<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;

class IdeaShow extends Component
{
    public Idea $idea;
    public int $voteCount;
    public bool $hasVoted;

    public function mount(Idea $idea, int $voteCount)
    {
        $this->idea = $idea;
        $this->voteCount = $voteCount;
        $this->hasVoted = $idea->isVotedByUser(auth()->user());
    }

    public function render()
    {
        return view('livewire.idea-show');
    }
}
