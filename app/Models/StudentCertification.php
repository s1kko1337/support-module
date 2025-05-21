<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCertification extends Model
{
    use HasFactory;

    protected $table = 'student_certifications';

    public $fillable = ['student_id', 'certification_id', 'passed'];

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class,'certification_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
