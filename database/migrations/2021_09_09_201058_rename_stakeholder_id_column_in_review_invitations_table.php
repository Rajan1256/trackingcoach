<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStakeholderIdColumnInReviewInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_invitations', function (Blueprint $table) {
            $table->dropForeign('review_invitations_stakeholder_id_foreign');
            $table->renameColumn('stakeholder_id', 'supporter_id');
            $table->foreign('supporter_id')
                ->references('id')->on('supporters')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('review_invitations', function (Blueprint $table) {
            //
        });
    }
}
