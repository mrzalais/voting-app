<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class CreateIdea extends Component
{
    public string $title = "";
    public int $category = 1;
    public string $description = "";

    protected array $rules = [
        'title' => 'required|min:4',
        'category' => 'required|exists:categories,id',
        'description' => 'required|min:4',
    ];

    public function createIdea()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->validate();
        $idea = Idea::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category,
            'status_id' => 1,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $idea->vote(auth()->user());

        session()->flash('success_message', 'Idea was added successfully!');

        $this->reset();

        return redirect()->route('idea.index');
    }

    public function render()
    {
        return view('livewire.create-idea', [
            'categories' => Category::all(),
        ]);
    }
}
