<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;

class IdeaShow extends Component
{
    public Idea $idea;
    public int $voteCount;

    public function mount(Idea $idea, $voteCount)
    {
        $this->idea = $idea;
        $this->voteCount = $voteCount;
    }

    public function render()
    {
        return view('livewire.idea-show');
    }
}
