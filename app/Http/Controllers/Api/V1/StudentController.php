<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Students\StoreStudentRequest;
use App\Http\Requests\Api\V1\Students\UpdateStudentRequest;
use App\Http\Resources\Api\V1\StudentCertificationResource;
use App\Http\Resources\Api\V1\StudentCharacteristicResource;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentCertification;
use App\Models\StudentCharacteristics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;

class StudentController extends Controller
{
    public static string $universityName = 'ФГБОУ ВО "Воронежский государственный технический университет"';
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

        \PhpOffice\PhpWord\Settings::setZipClass(Settings::PCLZIP);

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
            'ОТЧЕТ ПО СТУДЕНТУ',
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
        return response()->json([
            "message" => $students
        ],200);
    }

    public function groupList($groupId, $studentId)
    {
        $user = Auth::user();
        try {
            $student = $user->groups()->findOrFail($groupId)->students()->findOrFail($studentId);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "The group is not created or does not belong to you",
                "th" => $th->getMessage()
            ],404);
        }
        $studentData = StudentResource::make($student);

        \PhpOffice\PhpWord\Settings::setZipClass(Settings::PCLZIP);
        $phpWord = new PhpWord();

        // Стили документа
        $baseFont = ['name' => 'Times New Roman', 'size' => 14];
        $headerFont = array_merge($baseFont, ['bold' => true]);
        $paragraphStyle = ['alignment' => Jc::BOTH, 'spaceAfter' => 0];

        // Настройки страницы
        $section = $phpWord->addSection([
            'marginLeft' => 1134,
            'marginRight' => 567,
            'marginTop' => 1134,
            'marginBottom' => 1134
        ]);

        // Шапка документа
        $section->addText(
            self::$universityName,
            array_merge($baseFont, ['underline' => 'single']),
            ['alignment' => Jc::END]
        );
        $section->addTextBreak(2);

        $data = $studentData->toArray(\request());
        $certifications = $data["student_certifications"]->toArray(\request());


        // Заголовок отчета
        $section->addText(
            'ОТЧЕТ ПО СТУДЕНТУ',
            ['name' => 'Times New Roman', 'size' => 16, 'underline' => 'single'],
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // Основная таблица с данными студента
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
            'alignment' => Jc::CENTER
        ]);


        // Основные данные
        $rows = [
            ['ФИО', $data["surname"].' '.$data["name"].' '.$data["patronymic"]],
            ['Дата рождения', $data["date_of_birth"]],
            ['Телефон', $data["phone_number"]],
            ['Гражданство', $data["citizenship"]],
            ['Группа', $data["group_name"]],
            ['Форма обучения', $data["education_form"]],
            ['Образование', $data["education"]],
        ];

        foreach ($rows as $row) {
            $table->addRow();
            $table->addCell(3000)->addText($row[0], $baseFont, $paragraphStyle);
            $table->addCell(7000)->addText($row[1], $baseFont, $paragraphStyle);
        }
        $section->addTextBreak(1);
        $section->addText(
            'РЕЗУЛЬТАТЫ АТТЕСТАЦИИ',
            ['name' => 'Times New Roman', 'size' => 16, 'underline' => 'single'],
            ['alignment' => Jc::CENTER]
        );

        $section->addTextBreak(1);

        if (!empty($certifications)) {
            $certTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => Jc::CENTER
            ]);

            // Заголовки таблицы сертификаций
            $certTable->addRow();
            $certTable->addCell(4000)->addText('Аттестация', $headerFont, ['alignment' => Jc::CENTER]);
            $certTable->addCell(3000)->addText('Статус', $headerFont, ['alignment' => Jc::CENTER]);
            $certTable->addCell(3000)->addText('Дата сдачи', $headerFont, ['alignment' => Jc::CENTER]);

            // Данные сертификаций
            foreach ($certifications as $cert) {
                $certTable->addRow();
                $certTable->addCell(4000)->addText($cert["certification_name"], $baseFont);
                $certTable->addCell(3000)->addText($cert["passed"] ? 'Сдано' : 'Не сдано', $baseFont);
                $certTable->addCell(3000)->addText(Carbon::parse($cert["updated_at"])->toDateString(), $baseFont);
            }
        }
        // Блок куратора
        $section->addTextBreak(2);
        $section->addText(
            "Куратор группы " . $student->group->name,
            $baseFont,
            ['alignment' => Jc::END]
        );

        $section->addText(
            $user->surname . ' ' . $user->name,
            array_merge($baseFont, ['bold' => false]),
            ['alignment' => Jc::END]
        );

        try {
            $filename = 'reports/student_report_'.time().'.docx';
            $phpWord->save(storage_path('app/public/'.$filename), 'Word2007');

//            return response()->download(
//                storage_path('app/public/'.$filename),
//                'Student_Report_'.$studentData->surname.'.docx'
//            )->deleteFileAfterSend(true);
//            return response()->json([
//                "path" => storage_path('app/public/'.$filename)
//            ],200);


        } catch (\Exception $e) {
            Log::error('Document generation error: '.$e->getMessage());
            return response()->json(['error' => 'Document generation failed'], 500);
        }
        return new StudentResource($student);
    }
}
