<?php

namespace App\Http\Requests\Api\V1\StudentCharacteristics;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentCharacteristicRequest extends FormRequest
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
            'institution' => 'required|string',
            'student_id' => 'required|int|exists:students,id',
            'characteristics' => 'required|array',
            'passed' => 'required|boolean',
        ];
    }
}
