<?php

namespace App\Modules\Translator;

use App\Modules\Translator\Models\BladeTranslation;
use Carbon\Carbon;
use Countable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Translation\MessageSelector;

class BladeTranslator
{
    public function translate($textToTranslate, $replace = [], $locale = null)
    {
        if (config('topmind.debug_translations') == true) {
            return '';
        }

        $line = $this->getTextFromCacheOrDatabase($textToTranslate, $locale);

        return $this->applyBb($this->parse($line, $replace));
    }

    public function translateChoice($textToTranslate, $number, array $replace = [], $locale = null)
    {
        if (config('topmind.debug_translations') == true) {
            return '';
        }

        $line = $this->getTextFromCacheOrDatabase($textToTranslate, $locale);

        // If the given "number" is actually an array or countable we will simply count the
        // number of elements in an instance. This allows developers to pass an array of
        // items without having to count it on their end first which gives bad syntax.
        if (is_array($number) || $number instanceof Countable) {
            $number = count($number);
        }

        $replace['count'] = $number;

        return $this->makeReplacements(
            (new MessageSelector)->choose($line, $number, $locale), $replace
        );
    }

    private function getHashOne($textToTranslate)
    {
        return md5($textToTranslate);
    }

    private function getHashTwo($textToTranslate)
    {
        return md5(strlen($textToTranslate).'-'.substr($textToTranslate, 0, 10).'-'.$textToTranslate);
    }

    private function parse($line, $replace = [])
    {
        return $this->makeReplacements($line, $replace);
    }

    private function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        $replace = $this->sortReplacements($replace);

        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }

    private function sortReplacements($replace)
    {
        return (new Collection($replace))->sortBy(function ($value, $key) {
            return mb_strlen($key) * -1;
        })->all();
    }

    /**
     * @param $textToTranslate
     * @param $locale
     * @return mixed
     */
    protected function getTextFromCacheOrDatabase($textToTranslate, $locale): string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $hash1 = $this->getHashOne($textToTranslate);
        $hash2 = $this->getHashTwo($textToTranslate);
        $translations = BladeTranslation::getTranslations($locale);

        $result = data_get($translations, "{$hash1}-{$hash2}", null);

        if (! $result) {
            $model = BladeTranslation::firstOrNew([
                'hash_1' => $hash1,
                'hash_2' => $hash2,
            ]);
            $model->last_detected_at = Carbon::now();

            if (! $model->exists) {
                $model->original = $textToTranslate;
                $model->setTranslation('en', $textToTranslate);
            }

            $model->save();

            $result = $model->getTranslation($locale);
        }

        return $result;
    }

    protected function applyBb($string) {
//        return (new HtmlString(\Genert\BBCode\Facades\BBCode::convertToHtml(htmlspecialchars($string, ENT_QUOTES, 'UTF-8', true))));
        return (new HtmlString(\Genert\BBCode\Facades\BBCode::convertToHtml($string)));
    }
}
