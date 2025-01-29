<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Course extends Component
{

    private $locations;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->locations = \App\Models\Location::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.course')->with('locations', $this->locations);
    }
}
