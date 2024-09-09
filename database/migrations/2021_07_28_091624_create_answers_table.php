<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('scope');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stakeholder_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('review_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('question_id')->constrained();
            $table->foreignId('question_history_id')->constrained();
            $table->boolean('answer_boolean')->nullable();
            $table->decimal('answer_number', 10, 4)->nullable();
            $table->text('answer_text')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }
}
