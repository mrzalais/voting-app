<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\WithAuthRedirects;
use App\Models\Idea;
use Livewire\Component;

class IdeaShow extends Component
{
    use WithAuthRedirects;

    public Idea $idea;
    public int $voteCount;
    public bool $hasVoted;

    protected $listeners = [
        'statusWasUpdated',
        'statusWasUpdatedError',
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

    public function statusWasUpdatedError(): void
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

    public function vote(): void
    {
        if (auth()->guest()) {
            $this->redirectToLogin();
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
