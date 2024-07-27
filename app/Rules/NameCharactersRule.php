<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameCharactersRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[\p{Arabic}a-zA-Z0-9\sپگژچ]+$/u',$value)){
            $fail(__(':attribute must contain only english and persian characters.',[$attribute]));
        }
    }
}
