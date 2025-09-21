<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $label;
    public $name;
    public $type;
    public $required;
    public $value;
    public $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct($label, $name, $type = 'text', $required = false, $value = '', $placeholder = '')
    {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->value = $value ?? '';
        $this->placeholder = $placeholder ?? '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.input');
    }
}

