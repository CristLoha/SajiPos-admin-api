<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // Cek apakah user adalah admin
    private function isAdmin()
    {
        if (!in_array(auth()->user()->roles, ['admin', 'staff', 'user'])) {
            abort(403, 'Hanya admin yang bisa melakukan aksi ini.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $discounts = Discount::when($request->name, function ($query) use ($request) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%')
                  ->orWhere('code', 'like', '%'.$request->name.'%');
            });
        })
        ->when($request->status_filter, function ($query) use ($request) {
            $today = now()->format('Y-m-d');
            if ($request->status_filter == 'active') {
                $query->where('status', '!=', 'inactive')
                      ->where(function ($q) use ($today) {
                          $q->whereNull('start_date')
                            ->orWhere('start_date', '<=', $today);
                      })
                      ->where(function ($q) use ($today) {
                          $q->whereNull('expired_date')
                            ->orWhere('expired_date', '>=', $today);
                      });
            } elseif ($request->status_filter == 'expired') {
                $query->where('status', '!=', 'inactive')
                      ->whereNotNull('expired_date')
                      ->where('expired_date', '<', $today);
            } elseif ($request->status_filter == 'upcoming') {
                $query->where('status', '!=', 'inactive')
                      ->whereNotNull('start_date')
                      ->where('start_date', '>', $today);
            } elseif ($request->status_filter == 'inactive') {
                $query->where('status', 'inactive');
            }
        })
        ->paginate(10);

        return view('pages.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->isAdmin();
        return view('pages.discounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->isAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:discounts,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Nilai persentase tidak boleh lebih dari 100.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'expired_date' => 'nullable|date|after_or_equal:start_date',
            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $request->value > 50 && empty($value)) {
                        $fail('Maksimal diskon wajib diisi jika persentase di atas 50% (untuk mencegah kerugian besar).');
                    }
                    if ($request->type === 'percentage' && !empty($value) && $value <= 100) {
                        $fail('Batas maksimal diskon (Rupiah) terlalu kecil. Isikan dengan nominal Rupiah (misal 15000), bukan angka persentase, atau kosongkan saja.');
                    }
                },
            ],
            'min_transaction' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'fixed' && $value !== null && $value < $request->value) {
                        $fail('Minimal transaksi harus lebih besar atau sama dengan nilai potongan tetap.');
                    }
                },
            ],
            'quota' => 'nullable|integer|min:0',
        ]);

        Discount::create($request->all());

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $discount = Discount::findOrFail($id);

        return view('pages.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->isAdmin();
        $discount = Discount::findOrFail($id);

        return view('pages.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->isAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:discounts,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Nilai persentase tidak boleh lebih dari 100.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'expired_date' => 'nullable|date|after_or_equal:start_date',
            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $request->value > 50 && empty($value)) {
                        $fail('Maksimal diskon wajib diisi jika persentase di atas 50% (untuk mencegah kerugian besar).');
                    }
                    if ($request->type === 'percentage' && !empty($value) && $value <= 100) {
                        $fail('Batas maksimal diskon (Rupiah) terlalu kecil. Isikan dengan nominal Rupiah (misal 15000), bukan angka persentase, atau kosongkan saja.');
                    }
                },
            ],
            'min_transaction' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'fixed' && $value !== null && $value < $request->value) {
                        $fail('Minimal transaksi harus lebih besar atau sama dengan nilai potongan tetap.');
                    }
                },
            ],
            'quota' => 'nullable|integer|min:0',
        ]);

        $discount = Discount::findOrFail($id);
        $discount->update($request->all());

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->isAdmin();
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus!');
    }
}
