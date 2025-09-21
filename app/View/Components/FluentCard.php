<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FluentCard extends Component
{
    public $variant;
    public $title;
    public $subtitle;
    public $icon;
    public $href;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $variant = 'default',
        $title = null,
        $subtitle = null,
        $icon = null,
        $href = null
    ) {
        $this->variant = $variant;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->icon = $icon;
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.fluent-card');
    }
}
