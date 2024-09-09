<?php

namespace App\Topmind\Tests;

use App\Topmind\Tests\Exceptions\PropertyDoesNotExistException;

interface TestInterface
{
    /**
     * Return test name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return blade path to this tests views.
     *
     * @return string
     */
    public function getViewPath();

    /**
     * Get validation rules as array.
     *
     * @return array
     */
    public function getValidationRules();

    /**
     * Get upload collection names.
     *
     * @return array
     */
    public function getUploadCollections();

    /**
     * Get all public properties.
     *
     * @return array
     */
    public function getProperties();

    /**
     * @param  array|string  $property
     * @param  mixed|null  $value
     * @return void
     * @throws PropertyDoesNotExistException
     */
    public function set($property, $value = null);

    /**
     * @param  array|string  $property
     * @param  mixed|null  $default
     * @return mixed
     */
    public function get($property, $default = null);
}
