<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Illuminate\Http\Response;

class MarkIdeaAsNotSpam extends Component
{
    public Idea $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markAsNotSpam()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->idea->spam_reports = 0;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsNotSpam');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-not-spam');
    }
}
