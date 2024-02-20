<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Slider extends Component
{

    private $sliders;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->sliders = \App\Models\Slider::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.slider')->with([
            'sliders' => $this->sliders
        ]);
    }
}
