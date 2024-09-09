<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendBigDataExportMail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public $export, public $team)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'    => $this->via($notifiable),
            'team'   => $this->team,
            'export' => $this->export,
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
        return (new MailMessage)
            ->salutation(new HtmlString(__('Regards').',<br/>'.$this->team->company))
            ->subject('Your export is ready')
            ->greeting(__('Hi :first_name,', ['first_name' => $notifiable->first_name]))
            ->line(__('Your requested export is ready to be downloaded.'))
            ->action(__('To download page'), route('exports.index'));
    }
}
