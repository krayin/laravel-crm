<?php

namespace Webkul\WebForm\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // This regular expression allows phone numbers with the following conditions:
        // - The phone number can start with an optional "+" sign.
        // - After the "+" sign, there should be one or more digits.
        return preg_match('/^\+?\d+$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('web_form::app.validations.invalid-phone-number');
    }
}
