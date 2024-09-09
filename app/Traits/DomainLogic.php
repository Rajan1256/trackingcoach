<?php

namespace App\Traits;

use App\Modules\Team\Events\SwitchedCurrentTeam;

trait DomainLogic
{
    public static function checkCurrent(): bool
    {
        return static::current() !== null;
    }

    public static function current(): ?self
    {
        $containerKey = self::getServiceContainerKey();

        if (!app()->has($containerKey)) {
            return null;
        }

        return app($containerKey);
    }

    /**
     * @return string
     */
    public static function getServiceContainerKey(): string
    {
        return 'currentTeam';
    }

    public function makeCurrent(): self
    {
        if ($this->isCurrent()) {
            return $this;
        }

        static::forgetCurrent();

        app()->instance(self::getServiceContainerKey(), $this);

        event(new SwitchedCurrentTeam($this));

        return $this;
    }

    public function isCurrent(): bool
    {
        return optional(static::current())->id === $this->id;
    }

    public static function forgetCurrent(): ?self
    {
        $current = static::current();

        if (is_null($current)) {
            return null;
        }

        $current->forget();

        return $current;
    }

    public function forget(): self
    {
        app()->forgetInstance(self::getServiceContainerKey());

        return $this;
    }
}
