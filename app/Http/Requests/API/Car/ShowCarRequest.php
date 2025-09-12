<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Car;

use App\Http\Requests\Traits\Includable;
use Illuminate\Foundation\Http\FormRequest;

class ShowCarRequest extends FormRequest
{
    use Includable;

    /**
     * The allowed includes.
     *
     * @var list<string>
     */
    protected $allowedIncludes = ['user', 'modifications'];

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
            ...$this->allowedIncludes($this->allowedIncludes),
            'id' => ['required', 'integer', 'exists:cars,id'],
        ];
    }
}
