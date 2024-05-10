<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class IsActive implements ValidationRule
{
    public function __construct(private string $tableName)
    {
        
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $verified = DB::table($this->tableName)
                        ->select('active')
                        ->where('email', $value)
                        ->first();

        if ($verified->active == 0) {
            $fail('Your account is not active');
        }
    }
}
