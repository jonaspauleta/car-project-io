<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Modification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModificationRequest extends FormRequest
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
            'car_id' => ['nullable', 'integer', 'exists:cars,id'],
            'name' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'brand' => ['nullable', 'string'],
            'vendor' => ['nullable', 'string'],
            'installation_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
