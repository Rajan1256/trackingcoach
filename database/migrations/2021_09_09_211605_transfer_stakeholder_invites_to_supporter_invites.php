<?php

use App\Models\Invite;
use Illuminate\Database\Migrations\Migration;

class TransferStakeholderInvitesToSupporterInvites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Invite::withoutGlobalScopes()->where('type', '=', 'stakeholder_review_invite')->get()->each(function ($invite) {
            $invite->update([
                'type' => 'supporter_review_invite',
            ]);
        });
    }
}
