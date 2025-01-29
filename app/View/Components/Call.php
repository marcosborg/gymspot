<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Call extends Component
{

    private $calls;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->calls = \App\Models\Call::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.call')->with('calls', $this->calls);
    }
}
