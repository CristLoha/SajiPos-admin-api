<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Mengambil profil toko (nama, alamat, telepon)
     */
    public function getShopProfile()
    {
        $setting = Setting::first();
        
        return response()->json([
            'success' => true,
            'message' => 'Profil Toko',
            'data' => [
                'name' => $setting?->shop_name ?? 'SajiPOS',
                'address' => $setting?->shop_address ?? 'Jl. Raya Kasir No. 1',
                'phone' => $setting?->shop_phone ?? '081234567890',
                'logo_url' => $setting?->logo_url ? asset('storage/' . $setting->logo_url) : null,
                'show_phone_on_receipt' => $setting?->show_phone_on_receipt ?? true,
                'show_address_on_receipt' => $setting?->show_address_on_receipt ?? true,
                'show_logo_on_receipt' => $setting?->show_logo_on_receipt ?? false,
            ]
        ]);
    }

    /**
     * Memperbarui pengaturan toko (API)
     */
    public function updateShopProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'show_phone_on_receipt' => 'nullable|boolean',
            'show_address_on_receipt' => 'nullable|boolean',
            'show_logo_on_receipt' => 'nullable|boolean',
        ]);

        $setting = Setting::first();
        $data = [];
        if ($request->has('name')) $data['shop_name'] = $request->name;
        if ($request->has('phone')) $data['shop_phone'] = $request->phone;
        if ($request->has('address')) $data['shop_address'] = $request->address;
        if ($request->has('show_phone_on_receipt')) $data['show_phone_on_receipt'] = $request->show_phone_on_receipt;
        if ($request->has('show_address_on_receipt')) $data['show_address_on_receipt'] = $request->show_address_on_receipt;
        if ($request->has('show_logo_on_receipt')) $data['show_logo_on_receipt'] = $request->show_logo_on_receipt;

        if ($setting) {
            $setting->update($data);
        } else {
            $setting = Setting::create($data);
        }

        return $this->getShopProfile();
    }

    /**
     * Mengambil pengaturan perhitungan biaya
     */
    public function getCostCalculation()
    {
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'shipping_fee' => 0,
                'include_shipping_in_tax' => false,
                'service_fee' => 0,
                'include_service_fee_in_tax' => false,
                'tax_percentage' => 0
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan Perhitungan Biaya',
            'data' => [
                'shipping_fee' => $setting->shipping_fee,
                'include_shipping_in_tax' => $setting->include_shipping_in_tax,
                'service_fee' => $setting->service_fee,
                'include_service_fee_in_tax' => $setting->include_service_fee_in_tax,
                'tax_percentage' => $setting->tax_percentage
            ]
        ]);
    }

    /**
     * Memperbarui pengaturan perhitungan biaya
     */
    public function updateCostCalculation(Request $request)
    {
        $request->validate([
            'shipping_fee' => 'required|numeric|min:0',
            'include_shipping_in_tax' => 'required|boolean',
            'service_fee' => 'required|numeric|min:0',
            'include_service_fee_in_tax' => 'required|boolean',
            'tax_percentage' => 'required|numeric|min:0|max:100'
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create($request->only([
                'shipping_fee',
                'include_shipping_in_tax',
                'service_fee',
                'include_service_fee_in_tax',
                'tax_percentage'
            ]));
        } else {
            $setting->update($request->only([
                'shipping_fee',
                'include_shipping_in_tax',
                'service_fee',
                'include_service_fee_in_tax',
                'tax_percentage'
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => [
                'shipping_fee' => $setting->shipping_fee,
                'include_shipping_in_tax' => $setting->include_shipping_in_tax,
                'service_fee' => $setting->service_fee,
                'include_service_fee_in_tax' => $setting->include_service_fee_in_tax,
                'tax_percentage' => $setting->tax_percentage
            ]
        ]);
    }
}
