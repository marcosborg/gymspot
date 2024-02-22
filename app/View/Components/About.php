<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class About extends Component
{

    private $abouts;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->abouts = \App\Models\About::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.about')->with('abouts', $this->abouts);
    }
}
