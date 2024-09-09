<?php

namespace App\Modules\Translator\Models;

use App\Locale;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class BladeTranslation extends Model
{
    use UsesSystemConnection;

    protected $guarded = ['id'];

    /** @var array */
    protected $casts = ['text' => 'array', 'last_detected_at' => 'timestamp'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::saved(function (self $model) {
            $model->flushGroupCache();
        });

        static::deleted(function (self $model) {
            $model->flushGroupCache();
        });
    }

    public static function getTranslations(string $locale): array
    {
        return Cache::rememberForever(static::getCacheKey($locale), function () use ($locale) {
            return static::query()
                    ->whereNotNull('last_detected_at')
                    ->get()
                    ->reduce(function ($lines, self $t) use ($locale) {
                        Arr::set($lines, $t->hash_1.'-'.$t->hash_2, $t->getTranslation($locale));

                        return $lines;
                    }) ?? [];
        });
    }

    public static function getCacheKey($locale)
    {
        return "bladeTranslations.{$locale}";
    }

    /**
     * @param string $locale
     * @param string $value
     *
     * @return $this
     */
    public function setTranslation(string $locale, string $value)
    {
        $this->text = array_merge($this->text ?? [], [$locale => $value]);

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getTranslation(string $locale): string
    {
        return $this->text[$locale] ?? $this->text[config('app.fallback_locale')];
    }

    protected function getTranslatedLocales(): array
    {
        return array_keys($this->text);
    }

    protected function flushGroupCache()
    {
        foreach (config('topmind.supported_locales') as $locale) {
            Cache::forget(static::getCacheKey($locale));
        }
    }
}
