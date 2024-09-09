<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('folder_id')->nullable();
            $table->foreign('folder_id')
                ->references('id')->on('asset_folders')
                ->onDelete('cascade');

            $table->unsignedBigInteger('author_id');
            $table->foreign('author_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');

            $table->boolean('coach_can_access')->default(0);
            $table->boolean('tracker_can_access')->default(0);
            $table->boolean('physiologist_can_access')->default(0);
            $table->boolean('user_can_access')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
