<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StudentCharacteristics\StoreStudentCharacteristicRequest;
use App\Http\Requests\Api\V1\StudentCharacteristics\UpdateStudentCharacteristicRequest;
use App\Http\Resources\Api\V1\StudentCharacteristicResource;
use App\Models\Student;
use App\Models\StudentCharacteristics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
class StudentCharacteristicsController extends Controller
{
    public static string $universityName = 'ФГБОУ ВО "Воронежский государственный технический университет"';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StudentCharacteristicResource::collection(StudentCharacteristics::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentCharacteristicRequest $request)
    {
        \PhpOffice\PhpWord\Settings::setZipClass(Settings::PCLZIP);
        $data = $request->validated();
        $student = Student::findOrFail($data['student_id']);

        $phpWord = new PhpWord();

        // Стили
        $baseFont = ['name' => 'Times New Roman', 'size' => 14];
        $paragraphStyle = [
            'lineHeight' => 1.5,
            'indentation' => ['firstLine' => 567 * 1.25],
            'alignment' => Jc::BOTH
        ];

        // Секция документа
        $section = $phpWord->addSection([
            'marginLeft' => 1134,
            'marginRight' => 567,
            'marginTop' => 1134,
            'marginBottom' => 1134
        ]);

        // Название учреждения
        $section->addText(
            self::$universityName,
            array_merge($baseFont, ['underline' => 'single']),
            ['alignment' => Jc::END]
        );
        $section->addTextBreak(2);

        // Заголовок
        $section->addText(
            'ХАРАКТЕРИСТИКА',
            array_merge($baseFont, ['bold' => false]),
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(2);

        $studentText = "Студент " . $student->surname . ' ' . $student->name . ' ' . $student->patronymic . ", " .
            $student->date_of_birth . " г.р." . ", учебной группы " . $student->group->name;

        if (!empty($data['characteristics'])) {
            $studentText .= " " . $data['characteristics'][0];
            array_shift($data['characteristics']);
        }

        $section->addText($studentText, $baseFont, $paragraphStyle);

        foreach ($data['characteristics'] as $paragraph) {
            $section->addText($paragraph, $baseFont, $paragraphStyle);
            $section->addTextBreak(0);
        }

        // Блок куратора
        $section->addTextBreak(2);
        $section->addText(
            "Куратор группы " . $student->group->name,
            $baseFont,
            ['alignment' => Jc::END]
        );

        $user = Auth::user();
        $section->addText(
            $user->surname . ' ' . $user->name,
            array_merge($baseFont, ['bold' => false]),
            ['alignment' => Jc::END]
        );

        try {
            $filename = 'profiles/characteristic_'.time().'.docx';
            $phpWord->save(storage_path('app/public/'.$filename), 'Word2007');

            $dataToCreate = [
                'path' => $filename,
                'passed' => $data['passed'],
            ];

            $studentCharacteristics = StudentCharacteristics::create($dataToCreate);

            return StudentCharacteristicResource::make($studentCharacteristics);

        } catch (\Exception $e) {
            Log::error('Document generation error: '.$e->getMessage());
            return response()->json(['error' => 'Document generation failed'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $studentCharacteristics = StudentCharacteristics::query()->findOrFail($id);

        return new StudentCharacteristicResource($studentCharacteristics);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentCharacteristicRequest $request, $id)
    {
        $studentCharacteristics = StudentCharacteristics::query()->findOrFail($id);
        $studentCharacteristics->update($request->validated());
        return new StudentCharacteristicResource($studentCharacteristics);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $studentCharacteristics = StudentCharacteristics::query()->findOrFail($id);
        $filePath = $studentCharacteristics->path;
        Storage::disk('public')->delete($filePath);
        $studentCharacteristics->delete();
        return response()->json([
            "message" => "Student characteristics deleted"
        ],204);
    }
}
