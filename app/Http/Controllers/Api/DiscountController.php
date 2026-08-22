<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/discounts
     */
    public function available()
    {
        $today = now()->format('Y-m-d');
        $discounts = Discount::where('status', '!=', 'inactive')
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('expired_date')
                      ->orWhere('expired_date', '>=', $today);
            })
            ->where(function ($query) {
                $query->whereNull('code')->orWhere('code', '');
            })
            ->get();

        return response()->json([
            'success' => true,
            'message' => $discounts->isEmpty() ? 'Belum ada diskon otomatis' : 'List Data Diskon Otomatis',
            'data' => $discounts->isEmpty() ? [] : $discounts
        ], 200);
    }

    public function checkCode(Request $request)
    {
        $code = $request->query('kode');
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Kode promo harus diisi'], 400);
        }

        $today = now()->format('Y-m-d');
        $discount = Discount::where('code', $code)
            ->where('status', '!=', 'inactive')
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('expired_date')
                      ->orWhere('expired_date', '>=', $today);
            })
            ->first();

        if (!$discount) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid atau sudah kedaluwarsa'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil digunakan',
            'data' => $discount
        ], 200);
    }

    /**
     * Display a listing of the resource.
     * GET /api/discounts
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $today = now()->format('Y-m-d');

        $query = Discount::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status === 'upcoming') {
            $query->where('status', '!=', 'inactive')
                  ->whereNotNull('start_date')
                  ->where('start_date', '>', $today);
        } elseif ($status === 'expired') {
            $query->where('status', '!=', 'inactive')
                  ->whereNotNull('expired_date')
                  ->where('expired_date', '<', $today);
        } elseif ($status === 'active') {
            $query->where('status', '!=', 'inactive')
                  ->where(function ($q) use ($today) {
                      $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', $today);
                  })
                  ->where(function ($q) use ($today) {
                      $q->whereNull('expired_date')
                        ->orWhere('expired_date', '>=', $today);
                  });
        }

        $discounts = $query->get();

        return response()->json([
            'success' => true,
            'message' => $discounts->isEmpty() ? 'Belum ada diskon' : 'List Data Diskon',
            'data' => $discounts->isEmpty() ? [] : $discounts
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/discounts
     */
    public function store(Request $request)
    {
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

        $discount = Discount::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Diskon Berhasil Ditambahkan!',
            'data' => $discount
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/discounts/{id}
     */
    public function show($id)
    {
        $discount = Discount::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Diskon',
            'data' => $discount
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/discounts/{id}
     */
    public function update(Request $request, $id)
    {
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

        return response()->json([
            'success' => true,
            'message' => 'Diskon Berhasil Diperbarui!',
            'data' => $discount
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/discounts/{id}
     */
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diskon Berhasil Dihapus!',
            'data' => null
        ], 200);
    }
}
