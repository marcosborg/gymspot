<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Nav extends Component
{

    private $menus;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->menus = \App\Models\Menu::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav')->with('menus', $this->menus);
    }
}
