<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyScoresTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_scores');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('days_per_week');
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_history_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 6, 3)->unsigned();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->json('extra_data');
            $table->timestamps();
        });
    }
}
