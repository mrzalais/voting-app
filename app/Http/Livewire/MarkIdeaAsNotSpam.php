<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkIdeaAsNotSpam extends Component
{
    public Idea $idea;

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function markAsNotSpam(): void
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->idea->spam_reports = 0;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsNotSpam', 'Spam Counter was reset!');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-not-spam');
    }
}
