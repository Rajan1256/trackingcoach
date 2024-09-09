<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\Invite;
use App\Models\Team;
use App\Models\User;
use App\Reports\WeekReport;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;

class SendWeeklyReportToUser extends Notification
{
    /**
     * @var Invite
     */
    public $invite;

    /**
     * @var WeekReport
     */
    public $report;

    /**
     * @var Team
     */
    public $team;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new notification instance.
     *
     * @param  WeekReport  $report
     * @param  Invite  $invite
     */
    public function __construct(WeekReport $report, Invite $invite, Team $team)
    {
        $this->report = $report;
        $this->invite = $invite;
        $this->user = $report->customer;
        $this->team = $team;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }

    public function toDatabase($notifiable)
    {
        return [
            'via'    => $this->via($notifiable),
            'team'   => $this->team,
            'report' => $this->report,
            'week'   => $this->report->week,
            'year'   => $this->report->year,
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

        return ['mail'];
    }

    public function toFcm($notifiable)
    {
        $settings = $notifiable->getSettings($this->team);
        $message = new FcmMessage();

        $toDevices = Device::where('team_id', $this->team->id)->where('user_id',
            $notifiable->id)->get()->pluck('fcm_token')->toArray();

        $to = count($toDevices) ? $toDevices : $settings->paired_app_token;

        $message->to($to, !is_array($to))->content([
            'title'        => $this->team->name,
            'body'         => __('Your weekly scorecard'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'sound'        => 'default',
        ])->data([
            'page' => sprintf('weekly-reports/%d/%d', $this->report->year, $this->report->week),
        ]);

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
        $settings = $this->user->getSettings($this->team);
        $daysPerWeek = $settings->days_per_week;
        $data = $this->report->getData();

        $mail = (new MailMessage)
            ->subject(__('Your weekly scorecard for week :week', ['week' => $data->get('week')]))
            ->line(__('Hi :first_name,', ['first_name' => $settings->first_name]));

        if ($data->get('unique_days') == $daysPerWeek) {
            $mail->line(__('Excellent, you responded the maximum this week!'));
        } else {
            $mail->line(__('Well done, you responded :unique_days out of :days_per_week times!',
                ['unique_days' => $data->get('unique_days'), 'days_per_week' => $daysPerWeek]));
        }

        $mail->line(__('As you know, the more you respond, the better you can monitor your progress and the more you will stay on track.'));

        if ($data->get('unique_days') < $daysPerWeek) {
            $mail->line(__('Try to maintain or even improve next week!'));
        }

        $mail
            ->action(__('View scorecard'), route('code.show', $this->invite->code))
            ->line(__('Keep your eyes on the ball!'))
            ->salutation(new HtmlString(__('Regards').',<br/>'.$this->team->company));

        return $mail;
    }

    public function toMessagebird($notifiable)
    {
        $firstName = $notifiable->first_name;
        $link = route('code.show', $this->invite->code);

        $body = __("Hello :first_name, your weekly scorecard for week :week is available at :link.",
            ['first_name' => $firstName, 'week' => $this->report->week, 'link' => $link]);
        $body .= __('Keep your eyes on the ball!');

        return (new MessagebirdMessage())
            ->setBody($body)
            ->setRecipients($notifiable->getSettings($this->team)->phone);
    }
}
