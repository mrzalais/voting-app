<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;

class IdeaShow extends Component
{
    public Idea $idea;
    public int $voteCount;
    public bool $hasVoted;

    protected $listeners = [
        'statusWasUpdated',
        'ideaWasUpdated',
        'ideaWasMarkedAsSpam',
        'ideaWasMarkedAsNotSpam',
        'commentWasAdded',
        'commentWasDeleted',
    ];

    public function mount(Idea $idea, int $voteCount): void
    {
        $this->idea = $idea;
        $this->voteCount = $voteCount;
        $this->hasVoted = $idea->isVotedByUser(auth()->user());
    }

    public function statusWasUpdated(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasUpdated(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsSpam(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsNotSpam(): void
    {
        $this->idea->refresh();
    }

    public function commentWasAdded(): void
    {
        $this->idea->refresh();
    }

    public function commentWasDeleted(): void
    {
        $this->idea->refresh();
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
        return view('livewire.idea-show');
    }
}
