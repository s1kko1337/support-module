<?php

namespace Database\Seeders;

use App\Http\Resources\Api\V1\GroupResource;
use App\Models\Category;
use App\Models\Certification;
use App\Models\Event;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::factory()->create();
        Event::factory(5)->create();
        Student::factory(5)->create();
        Subject::factory(5)->create();
        Certification::factory(3)->create();
    }
}
