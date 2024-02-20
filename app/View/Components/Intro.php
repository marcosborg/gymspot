<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Intro extends Component
{

    private $steps;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->steps = \App\Models\Step::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.intro')->with('steps', $this->steps);
    }
}
