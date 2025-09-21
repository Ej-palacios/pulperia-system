<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FluentTable extends Component
{
    public $loading;
    public $striped;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($loading = false, $striped = true)
    {
        $this->loading = $loading;
        $this->striped = $striped;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.fluent-table');
    }
}
