<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $fillable = ['surname', 'name','patronymic', 'date_of_birth',
        'phone_number','citizenship','group_id','characteristic_id','education','education_form'];
    public function characteristic(): HasOne
    {
        return $this->hasOne(StudentCharacteristics::class);
    }

    public function studentCertifications(): HasMany
    {
        return $this->hasMany(StudentCertification::class, 'student_id');
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

}
