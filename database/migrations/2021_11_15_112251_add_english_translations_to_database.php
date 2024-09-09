<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\TranslationLoader\LanguageLine;

class AddEnglishTranslationsToDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $json = json_decode(file_get_contents(lang_path('en.json')));
        foreach ($json as $key => $value) {
            LanguageLine::create([
                'group' => '*',
                'key'   => $key,
                'text'  => ['en' => $value],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LanguageLine::delete();
    }
}
