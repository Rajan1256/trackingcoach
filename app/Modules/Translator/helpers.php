<?php

if (! function_exists('trans_text')) {
    function trans_text($textToTranslate, array $replace = [], $locale = null)
    {
        return resolve(\App\Modules\Translator\BladeTranslator::class)
            ->translate($textToTranslate, $replace, $locale);
    }
}

if (! function_exists('trans_text_choice')) {
    function trans_text_choice($textToTranslate, $number, array $replace = [], $locale = null)
    {
        return resolve(\App\Modules\Translator\BladeTranslator::class)
            ->translateChoice($textToTranslate, $number, $replace, $locale);
    }
}

if (! function_exists('locale_to_id')) {
    function locale_to_id($locale = null)
    {
        if (is_a($locale, \App\Locale::class)) {
            return $locale->id;
        }

        if ($found = \App\Locale::cached()->where('id', $locale)->first()) {
            return $found->id;
        }

        $locale = $locale ?? app()->getLocale();

        $fromCache = \App\Locale::cached()
            ->where('locale', $locale)
            ->first();

        return $fromCache ? $fromCache->id : null;
    }
}
