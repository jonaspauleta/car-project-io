<?php

declare(strict_types=1);

namespace App\Http\Requests\Modification;

use Illuminate\Foundation\Http\FormRequest;

class StoreModificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'brand' => ['nullable', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The modification name is required.',
            'category.required' => 'The modification category is required.',
            'installation_date.before_or_equal' => 'The installation date cannot be in the future.',
            'cost.min' => 'The cost cannot be negative.',
            'cost.max' => 'The cost cannot exceed $999,999.99.',
            'notes.max' => 'The notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
