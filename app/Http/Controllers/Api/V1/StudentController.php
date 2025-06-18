<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Students\StoreStudentRequest;
use App\Http\Requests\Api\V1\Students\UpdateStudentRequest;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    public static string $universityName = 'ФГБОУ ВО "Воронежский государственный технический университет"';
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return StudentResource::collection(Student::query()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        $student = Student::create($request);

        return StudentResource::make($student);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): StudentResource
    {
        return new StudentResource($student);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        $student->update($request->toArray());
        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): \Illuminate\Http\JsonResponse
    {
        $student->delete();
        return response()->json([
            "message" => "Student deleted"
        ], Response::HTTP_NO_CONTENT);
    }

    public function groupReport($groupId)
    {
        $user = Auth::user();
        try {
            $group = $user->groups()->findOrFail($groupId);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "The group is not created or does not belong to you"
            ],Response::HTTP_NOT_FOUND);
        }
        try {
            $students = $group->students;
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Group have not students",
                "th" => $th->getMessage()
            ],Response::HTTP_NOT_FOUND);
        }

        \PhpOffice\PhpWord\Settings::setZipClass(Settings::PCLZIP);
        $phpWord = new PhpWord();

        // Стили документа
        $baseFont = ['name' => 'Times New Roman', 'size' => 14];

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

        // Заголовок отчета
        $section->addText(
            'ОТЧЕТ ОБ АТТЕСТАЦИИ ГРУППЫ ' . $group->name,
            array_merge($baseFont, ['bold' => false]),
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

        // Заголовки таблицы
        $table->addRow();
        $table->addCell(5000)->addText('ФИО', $baseFont, ['alignment' => Jc::CENTER]);
        $table->addCell(2500)->addText('1 семестр', $baseFont, ['alignment' => Jc::CENTER]);
        $table->addCell(2500)->addText('2 семестр', $baseFont, ['alignment' => Jc::CENTER]);

        foreach ($students as $student) {
            $studentData = StudentResource::make($student);
            $data = $studentData->toArray(\request());
            $certifications = $data["student_certifications"]->toArray(\request());

            $firstSemesterDebt = false;
            $secondSemesterDebt = false;

            foreach ($certifications as $cert) {
                if (!$cert["updated_at"]) {
                    continue;
                }

                $date = Carbon::parse($cert["date"]);
                $month = $date->month;

                if (($month >= 9 && $month <= 12) || $month === 1) {
                    if ($cert["passed"]) {
                        $firstSemesterDebt = true;
                    }
                } elseif ($month >= 2 && $month <= 7) {
                    if ($cert["passed"]) {
                        $secondSemesterDebt = true;
                    }
                }
            }

            // Добавление строки
            $table->addRow();
            $table->addCell(5000)->addText(
                $student["surname"] . ' ' . $student["name"] . ' ' . $student["patronymic"],
                $baseFont,
                ['alignment' => Jc::CENTER]
            );

            $table->addCell(2500)->addText(
                $firstSemesterDebt ? 'Аттестован' : 'Не аттестован',
                $baseFont,
                ['alignment' => Jc::CENTER]
            );
            $table->addCell(2500)->addText(
                $secondSemesterDebt ? 'Аттестован' : 'Не аттестован',
                $baseFont,
                ['alignment' => Jc::CENTER]
            );
        }

        // Блок куратора
        $section->addTextBreak(2);
        $section->addText(
            "Куратор группы " . $group->name,
            $baseFont,
            ['alignment' => Jc::END]
        );

        $section->addText(
            $user->surname . ' ' . $user->name,
            array_merge($baseFont, ['bold' => false]),
            ['alignment' => Jc::END]
        );

        try {
            $filename = 'reports/group_cert_report_'.time().'.docx';
            $phpWord->save(storage_path('app/public/'.$filename), 'Word2007');

            return response()->download(
                storage_path('app/public/'.$filename),
                'group_cert_report_'.$group->name.'.docx'
            )->deleteFileAfterSend(true);


        } catch (\Exception $e) {
            Log::error('Document generation error: '.$e->getMessage());
            return response()->json(['error' => 'Document generation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function studentReport($groupId, $studentId)
    {
        $user = Auth::user();
        try {
            $student = $user->groups()->findOrFail($groupId)->students()->findOrFail($studentId);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "The group is not created or does not belong to you",
                "th" => $th->getMessage()
            ],Response::HTTP_NOT_FOUND);
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
            ['ФИО', ' ' . $data["surname"].' '.$data["name"].' '.$data["patronymic"]],
            ['Дата рождения', ' ' . $data["date_of_birth"]],
            ['Телефон', ' ' . $data["phone_number"]],
            ['Гражданство', ' ' . $data["citizenship"]],
            ['Группа', ' ' . $data["group_name"]],
            ['Форма обучения', ' ' . $data["education_form"]],
            ['Образование', ' ' . $data["education"]],
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
            $certTable->addCell(4000)->addText('Аттестация', $baseFont, ['alignment' => Jc::CENTER]);
            $certTable->addCell(3000)->addText('Статус', $baseFont, ['alignment' => Jc::CENTER]);
            $certTable->addCell(3000)->addText('Дата аттестации', $baseFont, ['alignment' => Jc::CENTER]);

            // Данные сертификаций
            foreach ($certifications as $cert) {
                $certTable->addRow();
                $certTable->addCell(4000)->addText(' ' . $cert["certification_name"], $baseFont, ['alignment' => Jc::CENTER]);
                $certTable->addCell(3000)->addText( $cert["passed"] ? ' Аттестован' : ' Не аттестован', $baseFont, ['alignment' => Jc::CENTER]);
                $certTable->addCell(3000)->addText(' ' . Carbon::parse($cert["date"])->toDateString(), $baseFont,['alignment' => Jc::CENTER]);
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

            return response()->download(
                storage_path('app/public/'.$filename),
                'Student_Report_'.$studentData->surname.'.docx'
            )->deleteFileAfterSend(true);


        } catch (\Exception $e) {
            Log::error('Document generation error: '.$e->getMessage());
            return response()->json(['error' => 'Document generation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
