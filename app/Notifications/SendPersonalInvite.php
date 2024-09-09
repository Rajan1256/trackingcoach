<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\Invite;
use App\Models\Team;
use Benwilkins\FCM\FcmMessage;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;

class SendPersonalInvite extends Notification
{
    /**
     * @var Carbon
     */
    public $date;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * @var Team
     */
    public $team;

    /**
     * Create a new notification instance.
     *
     * @param  Carbon  $date
     * @param  Invite  $invite
     */
    public function __construct(Carbon $date, Invite $invite, Team $team)
    {
        $this->team = $team;
        $this->date = $date;
        $this->invite = $invite;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'    => $this->via($notifiable),
            'team'   => $this->team,
            'date'   => $this->date,
            'invite' => $this->invite,
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
        $settings = $notifiable->getSettings($this->team);

        if ($settings->preferred_notification_methods) {
            switch ($settings->preferred_notification_methods['daily_invites'] ?? null) {
                case 'app':
                    return ['fcm', 'database'];
                    break;
                case 'sms':
                    return [MessagebirdChannel::class, 'database'];
                    break;
                case 'both':
                    return [MessagebirdChannel::class, 'mail', 'database'];
                    break;
                case 'mail':
                default;
                    return ['mail', 'database'];
            }
        }

        return ['mail', 'database'];
    }

    public function toFcm($notifiable)
    {
        $settings = $notifiable->getSettings($this->team);
        $message = new FcmMessage();
        $productName = $this->team->name;

        $toDevices = Device::where('team_id', $this->team->id)->where('user_id',
            $notifiable->id)->get()->pluck('fcm_token')->toArray();

        $to = count($toDevices) ? $toDevices : $settings->paired_app_token;

        $message->to($to, !is_array($to))->content([
            'title'        => $this->team->name,
            'body'         => __("Evaluate your day"),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'sound'        => 'default',
        ])->data([
            'page' => 'daily-questions',
        ])->priority('high');

        return $message;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $productName = $this->team->name;

        return (new MailMessage)
            ->salutation(new HtmlString(__('Regards').',<br/>'.$this->team->company))
            ->subject(__("Your :name tracklist!", ['name' => $productName]))
            ->greeting(__('Dear :first_name,', ['first_name' => $notifiable->first_name]))
            ->line(__('Here is the link to your daily question-list:'))
            ->action(__('Your daily tracklist'), route('code.show', ['code' => $this->invite->code]))
            ->line(__('If you fill in the form within the next 24 hours, scores will count towards the achievement of the scorecard.'))
            ->line($this->team->settings['signature_line'] ?? '');
    }

    public function toMessagebird($notifiable)
    {
        $firstName = $notifiable->first_name;
        $link = route('code.show', ['code' => $this->invite->code]);

        $body = __("Dear :first_name, here is the link to your daily question-list: :link. This link will expire in 24 hours. Regards, :company",
            ['first_name' => $firstName, 'link' => $link, 'company' => $this->team->company]);

        return (new MessagebirdMessage())
            ->setBody($body)
            ->setRecipients($notifiable->getSettings($this->team)->phone);
    }
}
