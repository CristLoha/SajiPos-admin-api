<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Jobs\BroadcastPromoJob;

class CampaignController extends Controller
{
    private function isAdmin()
    {
        if (!in_array(auth()->user()->roles, ['admin', 'staff', 'user'])) {
            abort(403, 'Hanya admin yang bisa melakukan aksi ini.');
        }
    }

    public function index(Request $request)
    {
        $campaigns = Campaign::when($request->name, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->paginate(10);

        return view('pages.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $this->isAdmin();
        $products = Product::where('status', true)->get();
        return view('pages.campaigns.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->isAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $campaign = Campaign::create($request->except('products'));

        if ($request->has('products')) {
            $campaign->products()->sync($request->products);
        }

        // ==========================================
        // FITUR NOMOR 3: BROADCAST PROMO KE PELANGGAN VIA FCM (DI-QUEUE)
        // ==========================================
        if ($campaign->is_active) {
            BroadcastPromoJob::dispatch($campaign);
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil ditambahkan!');
    }

    public function show(Campaign $campaign)
    {
        return view('pages.campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        $this->isAdmin();
        $products = Product::where('status', true)->get();
        return view('pages.campaigns.edit', compact('campaign', 'products'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->isAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $campaign->update($request->except('products'));

        if ($request->has('products')) {
            $campaign->products()->sync($request->products);
        } else {
            $campaign->products()->detach();
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil diperbarui!');
    }

    public function destroy(Campaign $campaign)
    {
        $this->isAdmin();
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil dihapus!');
    }
}
