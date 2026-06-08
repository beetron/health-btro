<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStat extends Model
{
    // Explicitly define table name due to underscore convention
    protected $table = 'daily_stats';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'log_date', // Updated from date_time
        'body_weight',
        'body_fat',
        'sleep_duration',
        'mood',
        'workout',
        'cardio',
        'notes',
    ];

    // Define casts for proper data types
    protected $casts = [
        'log_date' => 'date', // Updated from datetime to date
        'body_weight' => 'float',
        'body_fat' => 'float',
        'sleep_duration' => 'integer',
        'mood' => 'integer', // Added to guarantee integer format
        'workout' => 'boolean',
        'cardio' => 'boolean',
    ];

    // Define the inverse relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Rounding logic for body metrics
    public function setBodyWeightAttribute(float $value): void
    {
        $this->attributes['body_weight'] = round($value, 1);
    }

    public function setBodyFatAttribute(float $value): void
    {
        $this->attributes['body_fat'] = round($value, 1);
    }

    public function setSleepDurationAttribute(int $minutes): void
    {
        $this->attributes['sleep_duration'] = $minutes;
    }

    public function getSleepDurationAttribute(): int
    {
        // Fallback to prevent null errors
        return $this->attributes['sleep_duration'] ?? 0;
    }
}
