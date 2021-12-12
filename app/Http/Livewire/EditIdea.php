<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class EditIdea extends Component
{
    public Idea $idea;
    public string $title = "";
    public int $category = 1;
    public string $description = "";

    protected array $rules = [
        'title' => 'required|min:4',
        'category' => 'required|exists:categories,id',
        'description' => 'required|min:4',
    ];

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
        $this->title = $idea->title;
        $this->category = $idea->category_id;
        $this->description = $idea->description;
    }

    public function updateIdea(): void
    {
        if (auth()->guest() || auth()->user()->cannot('update', $this->idea)) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $this->validate();

        $this->idea->update([
            'title' => $this->title,
            'category_id' => $this->category,
            'description' => $this->description,
        ]);

        $this->emit('ideaWasUpdated', 'Idea was updated successfully');
    }

    public function render()
    {
        return view('livewire.edit-idea', [
            'categories' => Category::all(),
        ]);
    }
}
