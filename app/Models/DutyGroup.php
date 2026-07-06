<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyGroup extends Model
{
    protected $fillable = [
        'name',
        'composition',
    ];

    protected function casts(): array
    {
        return [
            'composition' => 'array',
        ];
    }
}
