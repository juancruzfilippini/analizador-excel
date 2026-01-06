<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'stored_path',
        'rows_total',
        'rows_ok',
        'rows_failed',
        'status',
        'notes',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(SchoolSnapshot::class);
    }
}
