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
        /**
         * Laravel hands a custom rule whatever the request carried, so an array arrives here
         * unchanged. `preg_match()` then throws an uncaught TypeError and the unauthenticated web
         * form answers 500 — disclosing a stack trace wherever `APP_DEBUG` is on — instead of
         * reporting a validation failure. A non-scalar value is simply not a phone number.
         */
        if (! is_scalar($value)) {
            return false;
        }

        // This regular expression allows phone numbers with the following conditions:
        // - The phone number can start with an optional "+" sign.
        // - After the "+" sign, there should be one or more digits.
        return preg_match('/^\+?\d+$/', (string) $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('web_form::app.validations.invalid-phone-number');
    }
}
