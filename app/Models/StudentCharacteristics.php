<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCharacteristics extends Model
{
    use HasFactory;

    protected $table = 'student_characteristics';

    public $fillable = ['path', 'passed', 'student_id'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class,'student_id','id');
    }
}
