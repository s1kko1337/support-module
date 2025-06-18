<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = ['group_id', 'name', 'type','result','date'];


    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
