<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingWebController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('pages.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string',
            'shop_phone' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('shop_name', 'shop_address', 'shop_phone');
        
        $data['show_phone_on_receipt'] = $request->has('show_phone_on_receipt');
        $data['show_address_on_receipt'] = $request->has('show_address_on_receipt');
        $data['show_logo_on_receipt'] = $request->has('show_logo_on_receipt');

        $setting = Setting::first();

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($setting && $setting->logo_url && \Storage::disk('public')->exists($setting->logo_url)) {
                \Storage::disk('public')->delete($setting->logo_url);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_url'] = $path;
        }

        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        return redirect()->route('settings.index')->with('success', 'Profil toko berhasil diperbarui.');
    }
}
