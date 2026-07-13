<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hazard;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Hazard::query();

        if ($search = $request->input('search')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('user_name', 'like', "%{$search}%")
                    ->orWhere('hazard_description', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('hazard_category', $category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reported_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reported_at', '<=', $request->input('date_to'));
        }

        $sortOrder = $request->input('sort', 'newest');
        if ($sortOrder === 'oldest') {
            $query->orderBy('reported_at', 'asc');
        } else {
            $query->orderBy('reported_at', 'desc');
        }

        $reports = $query->paginate(12)->withQueryString();

        $categories = [
            'Road Hazard',
            'Environmental Hazard',
            'Building Hazard',
        ];

        return view('reports.index', compact('reports', 'categories'));
    }

    public function show(Hazard $report)
    {
        return view('reports.show', ['report' => $report]);
    }

    public function export(Request $request)
    {
        $query = Hazard::query();

        if ($search = $request->input('search')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('user_name', 'like', "%{$search}%")
                    ->orWhere('hazard_description', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('hazard_category', $category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reported_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reported_at', '<=', $request->input('date_to'));
        }

        $reports = $query->orderByDesc('reported_at')->get();

        $filename = 'hazard_reports_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'User Name',
                'Hazard Category',
                'Hazard Description',
                'Latitude',
                'Longitude',
                'Location Name',
                'Device Info',
                'Reported At',
                'Created At',
            ]);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->id,
                    $report->user_name,
                    $report->hazard_category,
                    $report->hazard_description,
                    $report->latitude,
                    $report->longitude,
                    $report->location_name,
                    $report->device_info,
                    $report->reported_at->format('Y-m-d H:i:s'),
                    $report->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Hazard $report)
    {
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Hazard report deleted successfully.');
    }
}
