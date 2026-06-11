<?php

namespace App\Http\Controllers;

use App\Models\DailyStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    /**
     * Handle the incoming request to render health metric charts.
     */
    public function __invoke(Request $request): Response
    {
        $user = Auth::user();

        // Fetch unique months where logs exist for dropdown selection bounds
        $availableMonths = $user->dailyStats()
            ->selectRaw("DATE_FORMAT(log_date, '%Y-%m') as value")
            ->selectRaw("DATE_FORMAT(log_date, '%M %Y') as label")
            ->groupBy('value', 'label')
            ->orderBy('value', 'desc')
            ->get();

        // Establish default bounds matching the current calendar year
        $currentYear = Carbon::now()->year;
        $defaultStart = "{$currentYear}-01";
        $defaultEnd = "{$currentYear}-12";

        $startMonth = $request->query('start_month', $defaultStart);
        $endMonth = $request->query('end_month', $defaultEnd);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startMonth . '-01')->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m-d', $endMonth . '-01')->endOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $startMonth = $startDate->format('Y-m');
            $endMonth = $endDate->format('Y-m');
        }

        // Pull datasets sorted chronologically for proper chart plotting
        $dailyStats = $user->dailyStats()
            ->select(['id', 'log_date', 'body_weight', 'body_fat', 'sleep_duration', 'mood', 'workout', 'cardio'])
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('log_date', 'asc')
            ->get();

        return Inertia::render('Charts', [
            'dailyStats' => $dailyStats,
            'filters' => [
                'start_month' => $startMonth,
                'end_month' => $endMonth,
            ],
            'availableMonths' => $availableMonths,
        ]);
    }
}
