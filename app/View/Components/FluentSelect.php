<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FluentSelect extends Component
{
    public $label;
    public $name;
    public $required;
    public $disabled;
    public $error;
    public $help;
    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $label = '',
        $name = '',
        $required = false,
        $disabled = false,
        $error = '',
        $help = '',
        $value = ''
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->error = $error;
        $this->help = $help;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.fluent-select');
    }
}
