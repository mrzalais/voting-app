<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Livewire\WithPagination;

class IdeaComments extends Component
{
    use WithPagination;

    public Idea $idea;

    protected $listeners = ['commentWasAdded'];

    public function commentWasAdded(): void
    {
        $this->idea->refresh();
        $this->goToPage($this->idea->comments()->paginate()->lastPage());
    }

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function render()
    {
        return view('livewire.idea-comments', [
            'comments' => $this->idea->comments()->paginate()->withQueryString(),
        ]);
    }
}
