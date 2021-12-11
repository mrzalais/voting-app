<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class DeleteIdea extends Component
{
    public Idea $idea;

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function deleteIdea()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        Idea::destroy($this->idea->id);

        session()->flash('success_message', 'Idea was deleted successfully!');

        return redirect()->route('idea.index');
    }

    public function render()
    {
        return view('livewire.delete-idea');
    }
}
