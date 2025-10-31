<?php

namespace Webkul\Core\Contracts\Validations;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Decimal implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Allow empty values (null, empty string) to pass validation
        // This allows optional decimal fields to be left empty
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return;
        }

        if (! preg_match('/^\d*(\.\d{1,4})?$/', $value)) {
            $fail(trans('admin::app.validations.message.decimal', ['attribute' => $attribute]));
        }
    }
}
