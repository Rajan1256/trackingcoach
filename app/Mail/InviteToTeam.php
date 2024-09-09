<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteToTeam extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Team $team,
        public string $firstName,
        public string $lastName,
        public string $token
    ) {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.invite-user-to-team', [
            'team'      => $this->team,
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
            'token'     => $this->token,
        ]);
    }
}
