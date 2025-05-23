<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Groups\StoreGroupRequest;
use App\Http\Requests\Api\V1\Groups\UpdateGroupRequest;
use App\Http\Resources\Api\V1\GroupResource;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return GroupResource::collection(Group::with('curator')->paginate());
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request): array
    {
        $data = $request->validated();
        $user = Auth::user();
        $data['curator_id'] = $user->id;
        $group = $user->groups()->create($data);

        return GroupResource::make($group)->resolve();
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): GroupResource
    {
        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group): GroupResource
    {
        $group->update($request->all());
        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): \Illuminate\Http\JsonResponse
    {
        $group->delete();
        return response()->json([
            "message" => "Post deleted"
        ],204);
    }
}
