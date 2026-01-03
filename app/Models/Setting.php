<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Static method untuk get setting value dengan cache
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Static method untuk set setting value
     */
    public static function set($key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Cast value berdasarkan type
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'float':
            case 'double':
                return (float) $value;
            default:
                return $value;
        }
    }

    /**
     * Helper untuk check apakah hari tertentu adalah hari kerja
     */
    public static function isWorkDay($date)
    {
        $workDays = static::get('work_days', [1, 2, 3, 4, 5, 6]);
        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday

        return in_array($dayOfWeek, $workDays);
    }

    /**
     * Helper untuk get working days dalam bulan tertentu
     */
    public static function getWorkingDaysInMonth($year, $month)
    {
        $workDays = static::get('work_days', [1, 2, 3, 4, 5, 6]);
        $date = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $workingDays = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = \Carbon\Carbon::create($year, $month, $day);
            if (in_array($currentDate->dayOfWeek, $workDays)) {
                $workingDays[] = $day;
            }
        }

        return $workingDays;
    }

    /**
     * Helper untuk get non-working days
     */
    public static function getNonWorkingDays($year, $month)
    {
        $workDays = static::get('work_days', [1, 2, 3, 4, 5, 6]);
        $date = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $nonWorkingDays = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = \Carbon\Carbon::create($year, $month, $day);
            if (!in_array($currentDate->dayOfWeek, $workDays)) {
                $nonWorkingDays[] = $day;
            }
        }

        return $nonWorkingDays;
    }
}
