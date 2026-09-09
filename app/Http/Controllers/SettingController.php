<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_tagline' => ['nullable', 'string', 'max:255'],
            'store_address' => ['nullable', 'string'],
            'store_phone' => ['nullable', 'string', 'max:20'],
            'store_email' => ['nullable', 'email'],
            'ppn_enabled' => ['boolean'],
            'ppn_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            $group = in_array($key, ['store_name', 'store_tagline', 'store_address', 'store_phone', 'store_email'])
                ? 'store'
                : (in_array($key, ['ppn_enabled', 'ppn_percentage', 'currency_symbol'])
                    ? 'finance'
                    : 'receipt');

            Setting::set($key, $value, $group);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
