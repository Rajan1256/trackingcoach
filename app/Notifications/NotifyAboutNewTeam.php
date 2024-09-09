<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NotifyAboutNewTeam extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Team $team)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'  => $this->via($notifiable),
            'team' => $this->team,
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
            ->subject(__('Invited to a new team'))
            ->line(__('Dear :first_name,', ['first_name' => $notifiable->first_name]))
            ->line(__('You are added to the following team: :team', ['team' => $this->team->name]))
            ->action(__('Go to team'), route('dashboard'));
    }
}
