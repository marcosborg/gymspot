<?php

namespace App\View\Components;

use App\Models\ContentCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{

    private $uteis;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->uteis = ContentCategory::find(1)->load('pages')->pages;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.footer')->with('uteis', $this->uteis);
    }
}
