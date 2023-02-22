<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AlertComponent extends Component
{
    /**
     * alert 类型。
     *
     * @var string
     */
    public $type;

    /**
     * alert 消息。
     *
     * @var string
     */
    public $message;

    protected $except = [
        'type',
    ];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert');
    }
}
