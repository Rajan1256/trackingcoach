<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\Invite;
use App\Models\Team;
use App\Reports\MonthReport;
use Benwilkins\FCM\FcmMessage;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;

class SendMonthlyReportToUser extends Notification
{
    public $invite;

    public $report;

    public $team;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(MonthReport $report, Team $team)
    {
        $this->team = $team;
        $this->report = $report;
        $this->invite = Invite::newMonthlyReportInvite($this->report->customer, $this->report->year,
            $this->report->month);
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
            'report' => $this->report,
            'month'  => $this->report->getData()->get('month'),
            'year'   => $this->report->getData()->get('year'),
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

        $data = $this->report->getData();
        $monthObj = Carbon::createFromDate(date('Y'), $data->get('month'), 1);

        $toDevices = Device::where('team_id', $this->team->id)->where('user_id',
            $notifiable->id)->get()->pluck('fcm_token')->toArray();

        $to = count($toDevices) ? $toDevices : $settings->paired_app_token;

        $message->to($to, !is_array($to))->content([
            'title'        => $this->team->name,
            'body'         => __('Your monthly scorecard'),
            'click_action' => 'FCM_PLUGIN_ACTIVITY',
            'sound'        => 'default',
        ])->data([
            'page' => sprintf('monthly-reports/%d/%d', $monthObj->format('Y'), $monthObj->format('m')),
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
        $company = $this->team->company;
        $data = $this->report->getData();

        $monthObj = Carbon::createFromDate(date('Y'), $data->get('month'), 1);

        $mail = (new MailMessage)
            ->salutation(new HtmlString(__('Regards').',<br/>'.$this->team->company))
            ->subject(__('Your monthly report for :month', ['month' => $monthObj->format('F')]))
            ->greeting(__('Hi :first_name,', ['first_name' => $this->report->customer->first_name]))
            ->line(__('Thank you for participating in our :company program.', ['company' => $company]))
            ->line(__('After each month, you will receive a personal link to your monthly results.'))
            ->action(__('View report :month', ['month' => $monthObj->format('F')]),
                route('code.show', $this->invite->code))
            ->line(__('Keep your eyes on the ball!'));

        return $mail;
    }

    public function toMessagebird($notifiable)
    {
        $data = $this->report->getData();

        echo 'Sending a text';
        $firstName = $notifiable->first_name;
        $link = route('code.show', $this->invite->code);

        $monthObj = Carbon::createFromDate(date('Y'), $data->get('month'), 1);

        $body = __("Hello :first_name, your monthly report for :month is available at :link.",
            ['first_name' => $firstName, 'month' => $monthObj->format('F'), 'link' => $link]);
        $body .= __('Keep your eyes on the ball!');

        echo $body;

        return (new MessagebirdMessage())
            ->setBody($body)
            ->setRecipients($notifiable->getSettings($this->team)->phone);
    }
}
