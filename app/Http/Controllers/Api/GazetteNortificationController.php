<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GazetteNortificationResource;
use App\Models\GazetteNortification;
use Illuminate\Http\Request;

class GazetteNortificationController extends Controller
{
    /**
     * GET /api/gazettes
     * Query params:
     *  - per_page (int, 1..100, default 15)
     *  - q (search in title/short_description/description)
     *  - state (state slug; pass "All India" to skip filter)
     *  - from (YYYY-MM-DD) filter by updated_date >=
     *  - to (YYYY-MM-DD)   filter by updated_date <=
     *  - sort: one of updated_date_desc (default), updated_date_asc, effective_date_desc, effective_date_asc, created_desc
     */
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        $q = GazetteNortification::query();

        // state filter (stored as state slug in DB)
        if ($state = trim((string) $request->query('state', ''))) {
            if ($state !== 'All India') {
                $q->where('state', $state);
            }
        }

        // search
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // date range on updated_date
        if ($from = $request->query('from')) {
            $q->whereDate('updated_date', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $q->whereDate('updated_date', '<=', $to);
        }

        // sorting
        switch ($request->query('sort', 'updated_date_desc')) {
            case 'updated_date_asc':
                $q->orderBy('updated_date', 'asc'); break;
            case 'effective_date_desc':
                $q->orderBy('effective_date', 'desc'); break;
            case 'effective_date_asc':
                $q->orderBy('effective_date', 'asc'); break;
            case 'created_desc':
                $q->latest(); break;
            default:
                $q->orderBy('updated_date', 'desc');
        }

        return GazetteNortificationResource::collection(
            $q->paginate($perPage)->appends($request->query())
        );
    }

    /**
     * GET /api/gazettes/{slug}
     */
    public function show(string $slug)
    {
        $record = GazetteNortification::where('slug', $slug)->firstOrFail();
        return new GazetteNortificationResource($record);
    }
}
