<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActRuleForm;
use App\Http\Resources\ActRuleFormResource as ActRuleFormJsonResource;
use App\Http\Resources\ActFormResource;
use Illuminate\Support\Facades\Storage;

class ActRuleFormController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $includeForms = (string) $request->query('include') === 'forms';

        $q = ActRuleForm::query()->withCount('forms');

        if ($state = $request->query('state')) {
            if ($state !== 'All India') {
                $q->where('state', $state);
            }
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('title', 'like', "%{$search}%")
                   ->orWhere('short_description', 'like', "%{$search}%")
                   ->orWhere('act_desc', 'like', "%{$search}%")
                   ->orWhere('rule_desc', 'like', "%{$search}%");
            });
        }

        if ($includeForms) {
            $q->with(['forms' => fn ($fq) => $fq->orderBy('sort_order')]);
        }

        $acts = $q->orderBy('title')->paginate($perPage);

        // Collection uses {"data":[...], "links":..., "meta":...}
        return ActRuleFormJsonResource::collection($acts);
    }

    /**
     * GET /api/act-rule-forms/{id}
     * Returns full detail with nested forms and the exact shape you shared.
     */
    public function show(ActRuleForm $actRuleForm)
    {
        $actRuleForm->load(['forms' => fn ($q) => $q->orderBy('sort_order')]);
        // Single resource returns {"data": {...}} which matches your required shape
        return new ActRuleFormJsonResource($actRuleForm);
    }

    /**
     * GET /api/act-rule-forms/states
     */
    public function states()
    {
        return response()->json([
            'states' => [
                'All India','Andaman & Nicobar Islands','Andhra Pradesh','Arunachal Pradesh','Assam','Bihar',
                'Chandigarh','Chhattisgarh','Dadra & Nagar Haveli & Daman & Diu','Delhi','Goa','Gujarat','Haryana',
                'Himachal Pradesh','Jammu & Kashmir','Jharkhand','Karnataka','Kerala','Ladakh','Lakshadweep',
                'Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Puducherry',
                'Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttarakhand','Uttar Pradesh','West Bengal',
            ],
        ]);
    }
}
