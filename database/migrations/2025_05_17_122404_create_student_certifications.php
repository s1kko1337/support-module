<?php

use App\Models\Certification;
use App\Models\Student;
use App\Models\StudentCharacteristics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_certifications', function (Blueprint $table) {
            $table->foreignId('certification_id')->constrained('certifications','id');
            $table->foreignId('student_id')->constrained('students','id');
            $table->boolean('passed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_certifications');
    }
};
