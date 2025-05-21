<?php

namespace App\Http\Requests\Api\V1\StudentCertifications;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentCertificationRequest extends FormRequest
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
            'certification_id' => 'required|integer',
            'student_id' => 'required|integer',
            'passed' => 'sometimes|boolean',
        ];
    }
}
