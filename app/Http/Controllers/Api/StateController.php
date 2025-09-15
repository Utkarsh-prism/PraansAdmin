<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;

class StateController extends Controller
{
    /**
     * GET /api/states
     * Returns only id and name (for dropdowns)
     */
    public function index()
    {
        $states = State::active()
            ->ordered()
            ->get(['id', 'name']);

        return response()->json([
            'states' => $states,
        ]);
    }
}
