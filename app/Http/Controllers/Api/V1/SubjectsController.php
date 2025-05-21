<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Groups\StoreGroupRequest;
use App\Http\Requests\Api\V1\Groups\UpdateGroupRequest;
use App\Http\Requests\Api\V1\Subjects\StoreSubjectRequest;
use App\Http\Requests\Api\V1\Subjects\UpdateSubjectRequest;
use App\Http\Resources\Api\V1\GroupResource;
use App\Http\Resources\Api\V1\SubjectResource;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SubjectResource::collection(Subject::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request): array
    {
        $data = $request->validated();
        $subject = Subject::create($data);

        return SubjectResource::make($subject)->resolve();
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): SubjectResource
    {
        return new SubjectResource($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): SubjectResource
    {
        $subject->update($request->all());
        return new SubjectResource($subject);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): \Illuminate\Http\JsonResponse
    {
        $subject->delete();
        return response()->json([
            "message" => "Subject deleted"
        ],204);
    }
}
