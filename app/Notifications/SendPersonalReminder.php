<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\Invite;
use App\Models\Team;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Notifications\Notification;

class SendPersonalReminder extends Notification
{
    /**
     * @var Invite
     */
    public $invite;

    /**
     * @var int
     */
    public $try;

    public $team;

    /**
     * Create a new notification instance.
     *
     * @param  int  $try
     * @param  Invite  $invite
     */
    public function __construct(int $try, Invite $invite, Team $team)
    {
        $this->team = $team;
        $this->try = $try;
        $this->invite = $invite;
    }

    public function toFcm($notifiable)
    {
        $settings = $notifiable->getSettings($this->team);
        $message = new FcmMessage();
        $title = $this->try == 1 ? __('Remember to evaluate your day') : __('Remember to evaluate yesterday');

        $toDevices = Device::where('team_id', $this->team->id)->where('user_id',
            $notifiable->id)->get()->pluck('fcm_token')->toArray();

        $to = count($toDevices) ? $toDevices : $settings->paired_app_token;

        $message->to($to, !is_array($to))->content([
            'title'        => $this->team->name,
            'body'         => $title,
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'sound'        => 'default',
        ])->data([
            'page' => 'daily-questions',
        ])->priority('high');

        return $message;
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'    => $this->via($notifiable),
            'team'   => $this->team,
            'invite' => $this->invite,
            'try'    => $this->try,
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
            switch ($settings->preferred_notification_methods['daily_invites']) {
                case 'app':
                    return ['fcm', 'database'];
                    break;
                default:
                    return [];
                    break;
            }
        }

        return [];
    }

    /**
     *
     * /**
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
}
