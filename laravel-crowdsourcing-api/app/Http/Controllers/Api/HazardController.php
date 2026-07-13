<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHazardRequest;
use App\Models\Hazard;
use Illuminate\Http\JsonResponse;

class HazardController extends Controller
{
    /**
     * Return all hazards ordered by newest first.
     */
    public function index(): JsonResponse
    {
        $hazards = Hazard::query()
            ->orderByDesc('reported_at')
            ->get();

        return response()->json($hazards, 200);
    }

    /**
     * Store a newly created hazard in storage.
     */
    public function store(StoreHazardRequest $request): JsonResponse
    {
        $hazard = Hazard::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Hazard reported successfully.',
            'data' => $hazard,
        ], 201);
    }

    /**
     * Display the specified hazard.
     */
    public function show(Hazard $hazard): JsonResponse
    {
        return response()->json($hazard, 200);
    }

    /**
     * Remove the specified hazard from storage.
     */
    public function destroy(Hazard $hazard): JsonResponse
    {
        $hazard->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hazard deleted successfully.',
            'data' => [],
        ], 200);
    }
}
