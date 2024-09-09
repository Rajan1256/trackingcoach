<?php

namespace App\Topmind\Tests;

use App\Models\Test;
use App\Topmind\Tests\Exceptions\PropertyDoesNotExistException;
use Error;
use Exception;

abstract class TestAbstract implements TestInterface
{
    public function __wakeup()
    {
        $this->loadRelatedTests();
    }

    protected function loadRelatedTests()
    {
        foreach ((array) $this->getRelatedTests() as $testName) {
            try {
                $loaded = Test::find($this->get($testName));
                $this->set($testName, $loaded);
            } catch (Error $exception) {
            }
        }
    }

    protected function getRelatedTests()
    {
        return [];
    }

    /**
     * @param  array|string  $property
     * @param  mixed|null  $default
     * @return array|mixed|null
     */
    public function get($property, $default = null)
    {
        if (old($property)) {
            if (is_array(old($property))) {
                return collect(old($property))->filter(function ($e) {
                    return trim($e) ? true : false;
                })->toArray();
            }

            return old($property);
        }

        if (property_exists($this, $property) && $this->$property) {
            return $this->$property;
        }

        return $default;
    }

    /**
     * @param  array|string  $property
     * @param  mixed|null  $value
     * @return void
     * @throws PropertyDoesNotExistException
     */
    public function set($property, $value = null)
    {
        if (is_array($property)) {
            foreach ($property as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            if (!property_exists($this, $property)) {
                throw new PropertyDoesNotExistException($property.' does not exist');
            }
            if (is_array($value)) {
                $value = collect($value)->filter(function ($_v) {
                    return trim($_v) ? true : false;
                })->toArray();
            }

            $this->$property = $value;
        }
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return array_keys(get_object_vars($this));
    }

    public function vo2Max()
    {
        try {
            if ($this->wattMax <= 0 || $this->weight <= 0) {
                return 0;
            }
            $formula = ($this->wattMax * 0.0113 + 0.395) * 1000 / str_replace(',', '.', $this->weight);

            return number_format($formula, 1, '.', '');
        } catch (Exception $e) {
            return 0;
        }
    }
}
