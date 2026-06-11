<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyStatRequest;
use App\Models\DailyStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DailyStatController extends Controller
{
    /**
     * Render the dashboard with historical data payload.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();

        // Fetch only the unique months/years where logs exist for this specific user
        $availableMonths = $user->dailyStats()
            ->selectRaw("DATE_FORMAT(log_date, '%Y-%m') as value")
            ->selectRaw("DATE_FORMAT(log_date, '%M %Y') as label")
            ->groupBy('value', 'label')
            ->orderBy('value', 'desc')
            ->get();

        // Safely extract the target parameter filter or fall back to the current month string
        $currentDateString = Carbon::now()->format('Y-m');
        $selectedMonth = $request->query('month', $currentDateString);

        // Appending an explicit day boundary stops Carbon from auto-overflowing days on short months
        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $selectedMonth . '-01');
        } catch (\Exception $e) {
            $parsedDate = Carbon::now();
            $selectedMonth = $currentDateString;
        }

        // Fetch user data filtered by the target month selection, ordered newest to oldest
        $dailyStats = $user->dailyStats()
            ->select(['id', 'user_id', 'log_date', 'body_weight', 'body_fat', 'sleep_duration', 'mood', 'workout', 'cardio', 'notes'])
            ->whereYear('log_date', $parsedDate->year)
            ->whereMonth('log_date', $parsedDate->month)
            ->orderBy('log_date', 'desc')
            ->get();

        return Inertia::render('Dashboard', [
            'dailyStats' => $dailyStats,
            'filters' => [
                'month' => $selectedMonth,
            ],
            'availableMonths' => $availableMonths,
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyStat $dailyStat): RedirectResponse
    {
        // Prevent users from deleting someone else's log data
        if ($dailyStat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        /** @disregard intelephense error */
        $dailyStat->delete();

        return redirect()->back()->with('success', 'Daily log entry deleted successfully.');
    }
}
