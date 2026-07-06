<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_date',
        'service_type',
        'banner_image',
        'custom_service_type',
        'theme',
        'description',
        'speaker',
        'elder',
        'bible_reading',
        'start_time',
        'end_time',
        'place',
        'status',
        'liturgy_verses',
        'liturgy_songs',
        'duties',
        'attendance_male',
        'attendance_female',
        'offering_amount',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'liturgy_verses' => 'array',
            'liturgy_songs' => 'array',
            'duties' => 'array',
            'attendance_male' => 'integer',
            'attendance_female' => 'integer',
            'offering_amount' => 'decimal:2',
        ];
    }
}
