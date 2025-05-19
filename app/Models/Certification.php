<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certification extends Model
{
    protected $table = 'certifications';

    protected $fillable = ['subject_id', 'date'];
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function studentsCertifications(): HasMany
    {
        return $this->hasMany(StudentCertification::class);
    }
}
