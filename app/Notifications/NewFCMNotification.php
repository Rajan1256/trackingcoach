<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\Invite;
use App\Models\Team;
use Benwilkins\FCM\FcmMessage;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class NewFCMNotification extends Notification
{
    public Team $team;


    /**
     * Create a new notification instance.
     *
     * @param  Carbon  $date
     * @param  Invite  $invite
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        $toDevices = Device::where('team_id', $this->team->id)->where('user_id',
            $notifiable->id)->get()->pluck('fcm_token')->toArray();

        if (count($toDevices) === 0) {
            return;
        }

        $message = new FcmMessage();
        $message->to($toDevices)->content([
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
