<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon $service_date
 * @property bool $is_today
 * @property string|null $parsed_start_time
 * @property string|null $parsed_end_time
 */
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

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isToday(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->service_date->isToday());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function parsedStartTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (!$this->start_time) return null;
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->start_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1] . ':' . $matches[2];
            }
            return null;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function parsedEndTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (!$this->end_time) return null;
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->end_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1] . ':' . $matches[2];
            }
            return null;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isLive(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
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

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isFinished(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
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
