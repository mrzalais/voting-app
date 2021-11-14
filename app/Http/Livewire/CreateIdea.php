<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Http\Response;

class CreateIdea extends Component
{
    public string $title = "";
    public int $category = 1;
    public string $description = "";

    protected $rules = [
        'title' => 'required|min:4',
        'category' => 'required|exists:categories',
        'description' => 'required|min:4',
    ];

    public function createIdea()
    {
        if (auth()->check()) {
            $this->validate();
            Idea::create([
                'user_id' => auth()->id(),
                'category_id' => $this->category,
                'status_id' => 1,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            session()->flash('success_message', 'Idea was added succesfully.');

            $this->reset();

            return redirect()->route('idea.index');
        }
        abort(Response::HTTP_FORBIDDEN);
    }

    public function render()
    {
        return view('livewire.create-idea', [
            'categories' => Category::all(),
        ]);
    }
}
