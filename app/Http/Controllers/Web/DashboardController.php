<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hazard;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalReports = Hazard::count();
        $roadHazards = Hazard::where('hazard_category', 'Road Hazard')->count();
        $environmentalHazards = Hazard::where('hazard_category', 'Environmental Hazard')->count();
        $buildingHazards = Hazard::where('hazard_category', 'Building Hazard')->count();

        $latestReports = Hazard::orderByDesc('reported_at')->limit(6)->get();

        $categoryDistribution = Hazard::select('hazard_category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('hazard_category')
            ->get()
            ->pluck('count', 'hazard_category');

        $period = collect();
        $today = Carbon::today();
        for ($days = 13; $days >= 0; $days--) {
            $date = $today->copy()->subDays($days);
            $period->push($date->format('Y-m-d'));
        }

        $reportsPerDay = Hazard::selectRaw('DATE(reported_at) as day, COUNT(*) as count')
            ->where('reported_at', '>=', $today->copy()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day');

        $reportsPerDay = $period->mapWithKeys(fn ($day) => [
            $day => $reportsPerDay->get($day, 0),
        ]);

        $reportsByCategory = $categoryDistribution->map(fn ($count) => $count);

        $mostActiveUser = Hazard::select('user_name')
            ->selectRaw('COUNT(*) as reports')
            ->groupBy('user_name')
            ->orderByDesc('reports')
            ->first();

        $latestSubmission = Hazard::orderByDesc('reported_at')->first();

        return view('dashboard', compact(
            'totalReports',
            'roadHazards',
            'environmentalHazards',
            'buildingHazards',
            'latestReports',
            'categoryDistribution',
            'reportsPerDay',
            'reportsByCategory',
            'mostActiveUser',
            'latestSubmission'
        ));
    }
}
