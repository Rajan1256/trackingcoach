<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\VerifyUser;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class RegisteredNotification extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Team $team, public VerifyUser $verifyUser)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'         => $this->via($notifiable),
            'team'        => $this->team,
            'verify_user' => $this->verifyUser,
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
        config(['app.name' => 'Welcome']);
        return (new MailMessage)
            ->subject(__('Registration complete'))
            ->greeting(__('Dear :first_name,', ['first_name' => $notifiable->first_name]))
            ->line(__('Welcome to TrackingCoach!'))
            ->line(__('You have successfully registered your account: :email', ['email' => $notifiable->email]))
            ->line(new HtmlString(__('TrackingCoach has been designed to').' <strong>'.__('save').'</strong> '.__('coaches time,').' <strong>'.__('increase').'</strong> '.__('our impact and').' <strong>'.__('scale').'</strong> '.__('our coaching business.')))
            ->line('I’m thrilled by the chance to help you achieve this for your coaching too.')
            ->line(__('Please click the activation link to get started! (You can access your account at anytime with this button):'))
            ->action(__('Go to Your Team!'), 'https://'.$this->team->fqdn.'/verify/'.$this->verifyUser->token)
            ->line(__('For your records your \'username\' is: :email', ['email' => $notifiable->email]))
            ->line(__('If you forget your password, you can simply click on "Forgot Password" on the login screen.'))
            ->salutation(new HtmlString('<img src="'.asset('img/trackingcoach-stayconnected.png').'" style="max-width: 150px; max-height: 100px;"><br/> Anne-Johan Willemsen<br/>Executive Coach and Founder of TrackingCoach'));
    }
}
