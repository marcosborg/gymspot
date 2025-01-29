<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;


class Nav extends Component
{

    private $menus;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->menus = \App\Models\Menu::orderBy('position', 'asc')->get()->load('content_page')->map(function ($menu) {
            if ($menu->content_page) {
                $slug = Str::slug($menu->content_page->title);
                $menu->link = url('/') . "/cms/{$menu->content_page_id}/{$slug}";
            }
            return $menu;
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav')->with('menus', $this->menus);
    }
}
