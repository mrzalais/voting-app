<?php

namespace App\Http\Livewire;

use App\Jobs\NotifyAllVoters;
use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Illuminate\Http\Response;

class SetStatus extends Component
{
    public Idea $idea;
    public string $status;
    public $comment;
    public string $notifyAllVoters = '';

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->status = $this->idea->status_id;
    }

    public function setStatus()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $this->idea->status_id = $this->status;
        $this->idea->save();

        if ($this->notifyAllVoters) {
            NotifyAllVoters::dispatch($this->idea);
        }

        Comment::create([
            'user_id' => auth()->id(),
            'idea_id' => $this->idea->id,
            'status_id' => $this->status,
            'body' => $this->comment ?? 'No comment was added',
            'is_status_update' => true,
        ]);

        $this->reset('comment');

        $this->emit('statusWasUpdated');
    }

    public function render()
    {
        return view('livewire.set-status');
    }
}
