<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NeedsOne implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $values
     * @return bool
     */
    public function passes($attribute, $values)
    {
        $passes = false;
        foreach ($values as $value) {
            if (!empty($value)) {
                $passes = true;
            }
        }

        return $passes;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('At least one record is required.');
    }
}
