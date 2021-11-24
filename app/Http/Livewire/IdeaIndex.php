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
        $this->hasVoted = $idea->voted_by_user;
    }

    public function render()
    {
        return view('livewire.idea-index');
    }
}
