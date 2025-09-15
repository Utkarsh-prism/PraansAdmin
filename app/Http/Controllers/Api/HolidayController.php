<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Http\Resources\HolidayListResource;
use App\Http\Resources\HolidayShowResource;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $q = Holiday::query();

        if ($state = trim((string) $request->query('state', ''))) {
            $q->where('state', $state);
        }

        // Sirf required columns, no pagination => no "links/meta"
        $items = $q->select(['state', 'slug'])
                   ->orderBy('state')
                   ->orderBy('slug')
                   ->get();

        return HolidayListResource::collection($items);
    }

    /**
     * GET /api/holidays/{slug}
     * Returns single holiday with all details by slug.
     */
    public function show(string $slug)
    {
        $holiday = Holiday::query()
            ->with(['details' => function ($q) {
                $q->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->first();

        if (!$holiday) {
            return response()->json([
                'error' => true,
                'message' => 'Holiday not found.',
            ], 404);
        }

        return new HolidayShowResource($holiday);
    }
}