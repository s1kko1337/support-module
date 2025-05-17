<?php

use App\Models\Group;
use App\Models\StudentCharacteristics;
use App\Models\User;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('surname');
            $table->string('name');
            $table->string('patronymic');
            $table->date('date_of_birth');
            $table->string('phone_number');
            $table->string('citizenship')->default('Гражданство РФ');
            $table->foreignId('group_id')->constrained('groups', 'id');
            $table->foreignId('characteristic_id')->constrained('student_characteristics', 'id');
            $table->string('education'); //['общее среднее','среднее специальное']
            $table->string('education_form'); //['бюджет','целевое','платное']
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
