<?php

use Illuminate\Database\Migrations\Migration;

class UpdateMediaModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('media')
            ->where('model_type', 'App\Migrate\Test')
            ->update(['model_type' => 'App\Models\Test']);

        DB::table('media')
            ->where('model_type', 'App\Migrate\Asset')
            ->update(['model_type' => 'App\Models\Asset']);

        DB::table('media')
            ->where('model_type', 'App\Migrate\Team')
            ->update(['model_type' => 'App\Models\Team']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
