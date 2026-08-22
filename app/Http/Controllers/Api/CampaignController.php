<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the campaigns.
     */
    public function index(Request $request)
    {
        // By default return all active campaigns that haven't expired
        // Eager load products so the app knows which products are affected
        $campaigns = Campaign::with('products')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $campaigns
        ]);
    }

    /**
     * Display the specified campaign.
     */
    public function show($id)
    {
        $campaign = Campaign::with('products')->find($id);

        if (!$campaign) {
            return response()->json([
                'status' => 'error',
                'message' => 'Campaign tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ]);
    }
}
