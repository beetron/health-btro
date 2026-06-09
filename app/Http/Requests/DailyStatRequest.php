<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DailyStatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enforce authentication check
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Safely extract the route parameter ID if an update is occurring
        $dailyStatId = $this->route('daily_stat');

        return [
            'log_date' => [
                'required',
                'date',
                // Enforce daily uniqueness per user while ignoring current ID on update
                Rule::unique('daily_stats')
                    ->where(fn($query) => $query->where('user_id', $this->user()->id))
                    ->ignore($dailyStatId),
            ],
            'body_weight' => ['required', 'numeric', 'between:0,999.9'],
            'body_fat' => ['required', 'numeric', 'between:0,99.9'],
            'sleep_duration' => ['required', 'integer', 'min:0'],
            'mood' => ['required', 'integer', 'between:1,4'],
            'workout' => ['required', 'boolean'],
            'cardio' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
