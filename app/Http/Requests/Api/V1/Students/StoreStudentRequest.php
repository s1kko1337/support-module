<?php

namespace App\Http\Requests\Api\V1\Students;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'surname' => 'required|string',
            'name' => 'required|string',
            'patronymic' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'phone_number' => 'required|string',
            'citizenship' => 'required|string',
            'group_id' => 'required|integer|exists:groups,id',
            'characteristic_id' => 'sometimes|integer|exists:characteristics,id',
            'education' => 'required|string',
            'education_form' => 'required|string',
        ];
    }
}
