<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'school_id',
        'enrollment',
        'mb_calculated',
        'connectivity_status',
        'lan_status',
        'raw',
    ];

    protected $casts = [
        'raw' => 'array',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
