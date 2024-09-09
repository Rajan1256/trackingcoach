<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

class DipMailInBrandingSauce
{
    public function handle(MessageSending $event)
    {
        $team = current_team();

        if ($team) {
            $event->message->setFrom("notifications+{$team->id}@trackingcoach.com", $team->name);

            if ($replyTo = $team->settings['reply_to_email'] ?? null && !$event->message->getReplyTo()) {
                $event->message->setReplyTo([
                    $replyTo,
                ]);
            }
        } else {
            $event->message->setFrom("notifications@trackingcoach.com", 'Trackingcoach');
            $event->message->setReplyTo(['info@trackingcoach.com']);
        }
    }
}
