<?php

namespace App\Http\Requests\Api\V1\Students;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'surname' => 'sometimes|string',
            'name' => 'sometimes|string',
            'patronymic' => 'sometimes|string',
            'date_of_birth' => 'sometimes|date_format:Y-m-d',
            'phone_number' => 'sometimes|string',
            'citizenship' => 'sometimes|string',
            'group_id' => 'sometimes|integer|exists:groups,id',
            'education' => 'sometimes|string',
            'education_form' => 'sometimes|string',
        ];
    }
}
