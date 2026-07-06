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

    protected function isToday(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(fn () => $this->service_date->isToday());
    }

    protected function parsedStartTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(function () {
            if (!$this->start_time) return null;
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->start_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1] . ':' . $matches[2];
            }
            return null;
        });
    }

    protected function parsedEndTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(function () {
            if (!$this->end_time) return null;
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->end_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1] . ':' . $matches[2];
            }
            return null;
        });
    }

    protected function isLive(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(function () {
            if (!$this->is_today) return false;
            $start = $this->parsed_start_time;
            $end = $this->parsed_end_time;
            if (!$start || !$end) return false;

            $now = now();
            $startTime = \Illuminate\Support\Carbon::parse($this->service_date->format('Y-m-d') . ' ' . $start);
            $endTime = \Illuminate\Support\Carbon::parse($this->service_date->format('Y-m-d') . ' ' . $end);

            return $now->between($startTime, $endTime);
        });
    }

    protected function isFinished(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(function () {
            if (now()->startOfDay()->isAfter($this->service_date)) {
                return true;
            }
            if ($this->is_today) {
                $end = $this->parsed_end_time;
                if (!$end) return false;
                $endTime = \Illuminate\Support\Carbon::parse($this->service_date->format('Y-m-d') . ' ' . $end);
                return now()->isAfter($endTime);
            }
            return false;
        });
    }
}
