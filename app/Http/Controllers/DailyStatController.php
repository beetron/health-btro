<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyStatRequest;
use App\Models\DailyStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DailyStatController extends Controller
{
    /**
     * Render the dashboard with historical data payload.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Fetch user data ordered cleanly by chronological layout
        $dailyStats = $user->dailyStats()
            ->select(['id', 'user_id', 'log_date', 'body_weight', 'body_fat', 'sleep_duration', 'mood', 'workout', 'cardio', 'notes'])
            ->get();

        return Inertia::render('Dashboard', [
            'dailyStats' => $dailyStats,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DailyStatRequest $request): RedirectResponse
    {
        $request->user()->dailyStats()->create($request->validated());

        return redirect()->back()->with('success', 'Daily log entry saved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DailyStatRequest $request, DailyStat $dailyStat): RedirectResponse
    {
        // Enforce data ownership isolation
        if ($dailyStat->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $dailyStat->update($request->validated());

        return redirect()->back()->with('success', 'Daily log entry updated successfully.');
    }
}
