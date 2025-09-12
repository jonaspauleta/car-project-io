<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

final class KeysIn implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  string[]  $values
     * @return void
     */
    public function __construct(
        private array $values
    ) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $allowedKeys = array_flip($this->values);
        $unknownKeys = array_diff_key($value, $allowedKeys);

        return count($unknownKeys) === 0;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The selected :attribute key is invalid. Valid keys are: '.implode(', ', $this->values);
    }
}
