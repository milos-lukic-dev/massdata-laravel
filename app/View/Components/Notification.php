<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Notification component to display flash notification messages from the session.
 *
 * @class Notification
 */
class Notification extends Component
{
    /**
     * The notification message retrieved from the session.
     *
     * @var string|null
     */
    public ?string $notificationMessage;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->notificationMessage = session('notification');
    }

    /**
     * Get the view that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.notification');
    }
}
