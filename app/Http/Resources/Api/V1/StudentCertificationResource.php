<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentCertificationResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'certification_id' => $this->certification_id,
            'certification_name' => $this->certification->subject->name,
            'student_id' => $this->student_id,
            'student_fio' => $this->student->surname . ' '
                . $this->student->name . ' ' . $this->student->patronymic,
            'passed' => $this->passed,
            'date' => $this->certification->date,
            'created_at' => Carbon::parse($this->created_at,('Y-m-d H:i:s'))->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at,('Y-m-d H:i:s'))->toDateTimeString()
        ];
    }
}
