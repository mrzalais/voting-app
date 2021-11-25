<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;

class IdeaIndex extends Component
{
    public Idea $idea;
    public int $voteCount;
    public ?bool $hasVoted;

    public function mount(Idea $idea, $voteCount)
    {
        $this->idea = $idea;
        $this->voteCount = $voteCount;
        $this->hasVoted = $idea->isVotedByUser(auth()->user());
    }

    public function vote()
    {
        if (!auth()->check()) {
            return redirect(route('login'));
        }

        if ($this->hasVoted) {
            $this->idea->removeVote(auth()->user());
            $this->voteCount--;
            $this->hasVoted = false;
        } else {
            $this->idea->vote(auth()->user());
            $this->voteCount++;
            $this->hasVoted = true;
        }
    }

    public function render()
    {
        return view('livewire.idea-index');
    }
}
