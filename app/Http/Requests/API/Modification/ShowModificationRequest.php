<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Modification;

use App\Http\Requests\Traits\Includable;
use App\Repositories\ModificationRepository;
use Illuminate\Foundation\Http\FormRequest;

class ShowModificationRequest extends FormRequest
{
    use Includable;

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
            ...$this->allowedIncludes(ModificationRepository::ALLOWED_INCLUDES),
        ];
    }
}
