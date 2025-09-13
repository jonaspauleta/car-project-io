<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Modification;

use Illuminate\Foundation\Http\FormRequest;

class CreateModificationRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'brand' => ['nullable', 'string'],
            'vendor' => ['nullable', 'string'],
            'installation_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        if (method_exists($this, 'defaults')) {
            foreach ($this->defaults() as $key => $defaultValue) {
                if (! $this->has($key)) {
                    $this->merge([$key => $defaultValue]);
                }
            }
        }
    }

    protected function defaults()
    {
        return [
            'is_active' => true,
        ];
    }
}
