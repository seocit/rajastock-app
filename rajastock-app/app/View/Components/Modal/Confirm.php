<?php

namespace App\View\Components\Modal;

use Illuminate\View\Component;

class Confirm extends Component
{
    public string $name;
    public string $title;
    public string $message;
    public string $confirmText;
    public string $cancelText;
    public ?string $wireEvent;

    public function __construct(
        string $name,
        string $title = 'Are you sure?',
        string $message = 'This action cannot be undone.',
        string $confirmText = 'Delete',
        string $cancelText = 'Cancel',
        string $wireEvent = null
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->message = $message;
        $this->confirmText = $confirmText;
        $this->cancelText = $cancelText;
        $this->wireEvent = $wireEvent;
    }

    public function render()
    {
        return view('components.modal.confirm');
    }
}
