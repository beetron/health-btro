<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStat extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'date_time',
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
        'date_time' => 'datetime',
        'body_weight' => 'float',
        'body_fat' => 'float',
        'sleep_duration' => 'integer',
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
        return $this->attributes['sleep_duration'];
    }
}
