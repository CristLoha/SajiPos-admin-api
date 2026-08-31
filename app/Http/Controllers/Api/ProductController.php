<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/products
     */
    public function index(Request $request)
    {
        // Inisiasi query untuk mengambil produk aktif beserta relasi kategorinya, diskon, dan campaigns
        $query = Product::with(['category', 'campaigns'])->where('status', true);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Eksekusi query dan prioritaskan produk yang masuk campaign di atas
        $products = $query->get()->sortByDesc('is_campaign_active')->values();

        return response()->json([
            'success' => true,
            'message' => 'List Data Produk Aktif',
            'data' => $products
        ], 200);
    }

    /**
     * Display the specified resource.
     * GET /api/products/{id}
     */
    public function show($id)
    {
        $product = Product::with(['category', 'campaigns'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Produk',
            'data' => $product
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Dinaikin batasnya karena bakal dikompres di backend
            'status' => 'boolean',
            'is_best_seller' => 'boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '_' . time() . '.jpg'; // Selalu simpan sebagai JPG agar ukurannya kecil
            $path = storage_path('app/public/products');
            
            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Kompresi dan Resize menggunakan Intervention Image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file->getRealPath());
            
            // Resize maksimum lebar 800px (proporsional) lalu kompres kualitas ke 85%
            $image->scaleDown(width: 800);
            $image->toJpeg(85)->save($path . '/' . $filename);

            $data['image'] = 'products/' . $filename;
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Batas 5MB
            'status' => 'boolean',
            'is_best_seller' => 'boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $filename = uniqid() . '_' . time() . '.jpg';
            $path = storage_path('app/public/products');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Kompresi dan Resize menggunakan Intervention Image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file->getRealPath());
            
            $image->scaleDown(width: 800);
            $image->toJpeg(85)->save($path . '/' . $filename);

            $data['image'] = 'products/' . $filename;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}
