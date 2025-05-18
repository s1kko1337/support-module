<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{

    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'hours_of_practical',
        'hours_of_lectures',
    ];

    public function certification(): HasMany
    {
        return $this->hasMany(Certification::class);
    }
}
