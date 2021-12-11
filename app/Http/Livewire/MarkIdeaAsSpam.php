<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Symfony\Component\HttpFoundation\Response;
use Livewire\Component;

class MarkIdeaAsSpam extends Component
{
    public Idea $idea;

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function markAsSpam(): void
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->idea->spam_reports++;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsSpam', 'Idea was marked as spam!');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-spam');
    }
}
