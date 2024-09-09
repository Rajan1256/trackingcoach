<?php

namespace App\Notifications;

use App;
use App\Models\ReviewInvitation;
use App\Models\Supporter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;

class SendReviewSupporterReminder extends Notification
{
    /**
     * @var ReviewInvitation
     */
    public $reviewInvitation;

    /**
     * @var string
     */
    public $url;

    /**
     * Create a new notification instance.
     *
     * @param  ReviewInvitation  $reviewInvitation
     */
    public function __construct(ReviewInvitation $reviewInvitation)
    {
        $this->reviewInvitation = $reviewInvitation;

        $this->url = route('code.show', ['code' => $this->reviewInvitation->invite->code]);
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
            'via'              => $this->via($notifiable),
            'reviewInvitation' => $this->reviewInvitation,
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(Supporter $notifiable)
    {
        if ($notifiable->notification_method) {
            switch ($notifiable->notification_method) {
                case 'mail':
                    return ['mail', 'database'];
                    break;
                case 'sms':
                    return [MessagebirdChannel::class, 'database'];
                    break;
                case 'both':
                    return ['mail', MessagebirdChannel::class, 'database'];
                default:
                    return [];
            }
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        /** @var User $user */
        $user = $this->reviewInvitation->user;

        /** @var Supporter $stakeholder */
        $supporter = $this->reviewInvitation->supporter;

        /** @var Team $team */
        $team = $this->reviewInvitation->review->team;

        App::setLocale($supporter->locale);
        $mailMessage = (new MailMessage)
            ->subject(__(':company Progress Review :first_name',
                ['company' => $team->company, 'first_name' => $user->first_name]))
            ->greeting(__('Dear :first_name', ['first_name' => $supporter->first_name]))
            ->line(__('We already received valuable input on the development of :name. Your feedback and contribution to :first_name is very important too.',
                ['name' => $user->name, 'first_name' => $user->first_name]))
            ->line(__('Please note that the survey closing date is :closes_at. Click on the survey link hereunder and kindly take 5-10 minutes to complete the survey and submit it before the closing date.',
                ['closes_at' => $this->reviewInvitation->review->closes_at->format('m/d/Y')]))
            ->action(__('To the questionnaire'), $this->url)
            ->line(__('Your answers will be kept completely confidential and will be combined with at least 6 other supporters completing this survey. Only an anonymous summary report will be provided to :first_name.',
                ['first_name' => $user->first_name]))
            ->line(__('If you have any queries about the survey or would like to provide your remarks verbally please contact me via phone or e-mail. On behalf of :first_name thank you for your cooperation.',
                ['first_name' => $user->first_name]));

        $mailMessage->salutation(new HtmlString(__('Regards').',<br/>'.$team->company));

        return $mailMessage;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MessagebirdMessage
     */
    public function toMessagebird($notifiable)
    {
        /** @var User $client */
        $user = $this->reviewInvitation->user;

        /** @var Supporter $supporter */
        $supporter = $this->reviewInvitation->supporter;

        App::setLocale($supporter->locale);
        return (new MessagebirdMessage())
            ->setBody(__('Your feedback to :name is very important. The survey closing date is :closes_at. On behalf of :first_name, thank you for your contribution.',
                    [
                        'name'       => $user->name,
                        'closes_at'  => $this->reviewInvitation->review->closes_at->format('m/d/Y'),
                        'first_name' => $user->first_name,
                    ]).$this->url)
            ->setRecipients($notifiable->phone);
    }
}
