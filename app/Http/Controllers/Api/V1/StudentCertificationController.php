<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StudentCertifications\StoreStudentCertificationRequest;
use App\Http\Requests\Api\V1\StudentCertifications\UpdateStudentCertificationRequest;
use App\Http\Resources\Api\V1\StudentCertificationResource;
use App\Models\Group;
use App\Models\StudentCertification;
use App\Models\User;
use Illuminate\Http\Request;

class StudentCertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StudentCertificationResource::collection(StudentCertification::with('certification')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentCertificationRequest $request)
    {
        $studentCertification = StudentCertification::create($request);

        return new StudentCertificationResource($studentCertification);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentCertification $studentCertification)
    {
        return new StudentCertificationResource($studentCertification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentCertificationRequest $request, StudentCertification $studentCertification)
    {
        $studentCertification->update($request->toArray());
        return new StudentCertificationResource($studentCertification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentCertification $studentCertification)
    {
        $studentCertification->delete();
        return response()->json([
            "message" => "Student deleted"
        ],204);
    }
}
