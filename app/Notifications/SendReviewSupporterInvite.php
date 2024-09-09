<?php

namespace App\Notifications;

use App;
use App\Models\ReviewInvitation;
use App\Models\Supporter;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Messagebird\MessagebirdMessage;

class SendReviewSupporterInvite extends Notification
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
                case 'app':
                    return ['fcm', 'database'];
                    break;
                case 'sms':
                    return [MessagebirdChannel::class, 'database'];
                    break;
                case 'both':
                    return [MessagebirdChannel::class, 'mail', 'database'];
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
        $user = $this->reviewInvitation->user;
        $supporter = $this->reviewInvitation->supporter;

        App::setLocale($supporter->locale);
        return (new MailMessage)
            ->subject(__(':company Progress Review :first_name',
                ['company' => $supporter->team->company, 'first_name' => $user->first_name]))
            ->greeting(__('Dear Mr/Mrs :name,',
                ['name' => $supporter->name]))
            ->line(__(':name is currently participating in a :company Training. The goal of this training is to help :first_name become more effective as a leader within your organization.',
                ['name' => $user->name, 'company' => $supporter->team->company, 'first_name' => $user->first_name]))
            ->line(__(':first_name has asked us to involve you in this process as a supporter.',
                ['first_name' => $user->first_name]))
            ->line(__('Via the link below you can open a questionnaire and give your input on the development of :first_name in recent months. This questionnaire will take 5 to 10 minutes of your time.',
                ['first_name' => $user->first_name]))
            ->action(__('To the questionnaire'), $this->url)
            ->line(__('Your responses to this survey are completely confidential and anonymous, and will be combined with the feedback from at least 6 other supporters.'))
            ->line(__('If you have any questions or comments about the survey, please contact me by phone or email. Thank you, on behalf of :first_name for your cooperation.',
                ['first_name' => $user->first_name]))
            ->greeting($supporter->team->company)
            ->salutation(new HtmlString(__('Regards').',<br/>'.$supporter->team->company));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMessagebird($notifiable)
    {
        $user = $this->reviewInvitation->user;
        $supporter = $this->reviewInvitation->supporter;

        App::setLocale($supporter->locale);
        return (new MessagebirdMessage())
            ->setBody(__(':name is currently taking part in a :company Training, and has asked us to involve you as a supporter in this process. In following short questionnaire you can give your valuable feedback on :first_name\'s leadership development.',
                    ['company' => $supporter->team->company, 'name' => $user->name, 'first_name' => $user->first_name])
                .$this->url)
            ->setRecipients($notifiable->phone);
    }
}
