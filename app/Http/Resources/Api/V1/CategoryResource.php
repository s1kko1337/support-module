<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryRecource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'postCount' => $this->posts()->count(),
            'created_at' => Carbon::parse($this->created_at,('Y-m-d H:i:s'))->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at,('Y-m-d H:i:s'))->toDateTimeString()
        ];
    }
}
