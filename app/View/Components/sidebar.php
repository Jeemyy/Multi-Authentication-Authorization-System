<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;
    public $icon;
    public $href;
    public function __construct($title = null, $icon = null, $href = null)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
