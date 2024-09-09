<?php

use App\Models\Answer;
use App\Modules\Team\Scopes\TeamAwareScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('team_id')->after('user_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Answer::withoutGlobalScopes([TeamAwareScope::class])->with('question')->each(function ($answer) {
            $answer->team_id = $answer->question()->withoutGlobalScopes([TeamAwareScope::class])->first()->team_id;
            $answer->timestamps = false;
            $answer->save();
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('answers', function (Blueprint $table) {
            //
        });
    }
}
