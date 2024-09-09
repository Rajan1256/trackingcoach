<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class UserConsentUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $consents, public $team)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'      => $this->via($notifiable),
            'consents' => $this->consents,
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->markdown('mails.updated-consent', [
                'user'     => $notifiable,
                'consents' => $this->consents,
                'team'     => $this->team,
            ])
            ->cc($this->team->settings['reply_to_email'], $this->team->name);

        foreach ($this->consents as $consent) {
            $media = $consent->getFirstMedia('pdf');

            if ($media && Storage::disk($media->getDiskDriverName())
                    ->has($media->getPath())) {
                $message->attachData(Storage::disk($media->getDiskDriverName())
                    ->get($media->getPath()), $media->file_name);
            }
        }

        return $message;
    }
}
