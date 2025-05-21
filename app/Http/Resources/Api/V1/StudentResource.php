<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'surname'=>$this->surname,
            'name' => $this->name,
            'patronymic' => $this->patronymic,
            'date_of_birth' => Carbon::parse($this->date_of_birth)->format('Y-m-d H:i:s'),
            'phone_number' => $this->phone_number,
            'citizenship' => $this->citizenship,
            'group_id' => $this->group_id,
            'group_name' => $this->group->name,
            'characteristic_id' => $this->characteristic->id,
            'education' => $this->education,
            'education_form' => $this->education_form,
            'student_certifications' => StudentCertificationResource::collection(
                $this->studentCertifications
            ),
            'created_at' => Carbon::parse($this->created_at,('Y-m-d H:i:s'))->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at,('Y-m-d H:i:s'))->toDateTimeString()
        ];
    }
}
