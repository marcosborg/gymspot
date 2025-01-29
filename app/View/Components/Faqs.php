<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Faqs extends Component
{

    private $faq_categories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->faq_categories = \App\Models\FaqCategory::all()->load('faq_questions');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.faqs')->with('faq_categories', $this->faq_categories);
    }
}
