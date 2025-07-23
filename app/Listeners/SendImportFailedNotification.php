<?php

namespace App\Listeners;

use App\Events\ImportFailed;
use App\Mail\ImportErrorsMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

/**
 * Listener that handles ImportFailed events by sending notification emails.
 *
 * @class SendImportFailedNotification
 */
class SendImportFailedNotification
{
    /**
     * Handle the event.
     *
     * @param ImportFailed $event
     * @return void
     */
    public function handle(ImportFailed $event): void
    {
        $user = User::find($event->import->user_id);

        if (!empty($user) && $user->email) {
            Mail::to($user->email)->send(new ImportErrorsMail($event->import, $event->errorRows));
        }
    }
}
