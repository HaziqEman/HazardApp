<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hazard;

class MapController extends Controller
{
    public function index()
    {
        $hazards = Hazard::orderByDesc('reported_at')->get();

        return view('map', compact('hazards'));
    }
}
