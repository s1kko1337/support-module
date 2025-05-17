<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'name' => $this->name,
            'curator_id' => $this->curator_id,
            'number_of_students' => $this->number_of_students,
            'created_at' => Carbon::parse($this->created_at,('Y-m-d H:i:s'))->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at,('Y-m-d H:i:s'))->toDateTimeString()
        ];
    }
}
