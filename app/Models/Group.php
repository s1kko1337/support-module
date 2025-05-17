<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'curator_id',
        'number_of_students',
    ];

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id', 'id');
    }
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
