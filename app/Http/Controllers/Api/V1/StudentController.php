<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Students\StoreStudentRequest;
use App\Http\Requests\Api\V1\Students\UpdateStudentRequest;
use App\Http\Resources\Api\V1\StudentCertificationResource;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StudentResource::collection(Student::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $student = Student::create($data);

        return StudentResource::make($student);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return new StudentResource($student);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json([
            "message" => "Student deleted"
        ],204);
    }

    public function report($id)
    {
        $user = Auth::user();
        try {
            $group = $user->groups()->findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "The group is not created or does not belong to you"
            ],404);
        }
        $students = StudentResource::collection($group->students);
//        foreach ($students as $student) {
//            $student->studentCertifications = StudentCertificationResource::collection($student->studentCertifications);
//        }
        return response()->json([
            "message" => $students
        ],200);
    }

    public function groupList(Group $group)
    {

    }
}
