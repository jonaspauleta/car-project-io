<?php

declare(strict_types=1);

namespace App\Http\Requests\Frontend\Car;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'nickname' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:17', 'min:17'],
            'image' => ['nullable', 'image', 'max:10240'], // Max 10MB
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'make.required' => 'The car make is required.',
            'model.required' => 'The car model is required.',
            'year.required' => 'The car year is required.',
            'year.min' => 'The car year must be at least 1900.',
            'year.max' => 'The car year cannot be in the future.',
            'vin.min' => 'The VIN must be exactly 17 characters.',
            'vin.max' => 'The VIN must be exactly 17 characters.',
            'image.image' => 'The uploaded file must be an image.',
            'image.max' => 'The image size cannot exceed 10MB.',
            'notes.max' => 'The notes cannot exceed 1000 characters.',
        ];
    }
}
