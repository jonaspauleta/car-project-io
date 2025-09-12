<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Car;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
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
            'make' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'nickname' => ['nullable', 'string'],
            'vin' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
