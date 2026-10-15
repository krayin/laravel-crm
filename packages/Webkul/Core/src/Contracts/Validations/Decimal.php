<?php

namespace Webkul\Core\Contracts\Validations;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Decimal implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * The value arrives as the request carried it, so an array reaches `preg_match()` unchanged
         * and throws an uncaught TypeError — a 500 rather than a validation failure, which on the
         * unauthenticated web form discloses a stack trace wherever `APP_DEBUG` is on. A non-scalar
         * value is not a decimal.
         */
        if (! is_scalar($value)) {
            $fail(trans('admin::app.validations.message.decimal', ['attribute' => $attribute]));

            return;
        }

        if (! preg_match('/^\d*(\.\d{1,4})?$/', (string) $value)) {
            $fail(trans('admin::app.validations.message.decimal', ['attribute' => $attribute]));
        }
    }
}
