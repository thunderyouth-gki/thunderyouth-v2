<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * @property Carbon $service_date
 * @property bool $is_today
 * @property string|null $parsed_start_time
 * @property string|null $parsed_end_time
 */
/**
 * @property int $id
 * @property string|null $theme
 * @property string|null $speaker
 * @property string|null $place
 * @property Carbon $service_date
 * @property string $start_time
 * @property string $end_time
 * @property string $service_type
 * @property string|null $custom_service_type
 * @property string|null $description
 * @property string|null $banner_image
 * @property bool $is_live
 * @property bool $is_finished
 * @property string|null $attendance_otp
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

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
        'attendance_otp',
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
     * @return Attribute<bool, never>
     */
    protected function isToday(): Attribute
    {
        return Attribute::make(get: fn () => $this->service_date->isToday());
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function parsedStartTime(): Attribute
    {
        return Attribute::make(get: function () {
            if (! $this->start_time) {
                return null;
            }
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->start_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1].':'.$matches[2];
            }

            return null;
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function parsedEndTime(): Attribute
    {
        return Attribute::make(get: function () {
            if (! $this->end_time) {
                return null;
            }
            preg_match('/(\d{1,2})[:.](\d{2})/', $this->end_time, $matches);
            if (count($matches) >= 3) {
                return $matches[1].':'.$matches[2];
            }

            return null;
        });
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function isLive(): Attribute
    {
        return Attribute::make(get: function () {
            if (! $this->is_today) {
                return false;
            }
            $start = $this->parsed_start_time;
            $end = $this->parsed_end_time;
            if (! $start || ! $end) {
                return false;
            }

            $now = now();
            $startTime = Carbon::parse($this->service_date->format('Y-m-d').' '.$start);
            $endTime = Carbon::parse($this->service_date->format('Y-m-d').' '.$end);

            return $now->between($startTime, $endTime);
        });
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function isFinished(): Attribute
    {
        return Attribute::make(get: function () {
            if (now()->startOfDay()->isAfter($this->service_date)) {
                return true;
            }
            if ($this->is_today) {
                $end = $this->parsed_end_time;
                if (! $end) {
                    return false;
                }
                $endTime = Carbon::parse($this->service_date->format('Y-m-d').' '.$end);

                return now()->isAfter($endTime);
            }

            return false;
        });
    }

    /**
     * Generate a new 6-digit OTP for attendance.
     */
    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update(['attendance_otp' => $otp]);

        return $otp;
    }

    /**
     * Get the signed URL for QR code verification.
     *
     * @param  bool  $withOtp  Whether to include the OTP in the URL (for Admin PPT QR)
     */
    public function qrVerificationUrl(bool $withOtp = true): string
    {
        $params = ['service' => $this->id];

        if ($withOtp && $this->attendance_otp) {
            $params['otp'] = $this->attendance_otp;
        }

        // Use relative URL for the signature so that host/port mismatches
        // between CLI (Tinker) and Web (Browser) are ignored.
        $relativeUrl = URL::signedRoute('attendance.qr', $params, null, false);

        return url($relativeUrl);
    }
}
