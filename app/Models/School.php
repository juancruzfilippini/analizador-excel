<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'cue',
        'name',
        'province',
        'department',
        'city',
        'address',
        'lat',
        'lng',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(SchoolSnapshot::class);
    }
}
