<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');
        $paymentMethods = PaymentMethod::orderBy('sort_order')->orderBy('name')->get();

        return view('settings.index', compact('settings', 'paymentMethods'));
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

    public function storePaymentMethod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', 'unique:payment_methods,code'],
            'is_cash' => ['boolean'],
        ]);

        $code = ! empty($validated['code'])
            ? Str::slug($validated['code'], '_')
            : Str::slug($validated['name'], '_');

        // Pastikan kode unik jika dihasilkan otomatis
        if (PaymentMethod::where('code', $code)->exists()) {
            $code = $code.'_'.uniqid();
        }

        $maxSort = PaymentMethod::max('sort_order') ?? 0;

        PaymentMethod::create([
            'name' => $validated['name'],
            'code' => $code,
            'is_cash' => $request->boolean('is_cash'),
            'is_active' => true,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('settings.index')->with('success', 'Metode pembayaran "'.$validated['name'].'" berhasil ditambahkan.');
    }

    public function togglePaymentMethod(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('settings.index')->with('success', "Metode pembayaran {$paymentMethod->name} berhasil {$status}.");
    }

    public function destroyPaymentMethod(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->code === 'cash') {
            return redirect()->route('settings.index')->with('error', 'Metode Tunai (Cash) adalah metode pembayaran utama dan tidak boleh dihapus.');
        }

        // Cek apakah pernah digunakan dalam transaksi
        $usedCount = Transaction::where('payment_method', $paymentMethod->code)->count();
        if ($usedCount > 0) {
            $paymentMethod->update(['is_active' => false]);

            return redirect()->route('settings.index')->with('success', "Metode pembayaran {$paymentMethod->name} pernah digunakan pada transaksi, sehingga statusnya diubah menjadi Nonaktif.");
        }

        $paymentMethod->delete();

        return redirect()->route('settings.index')->with('success', "Metode pembayaran {$paymentMethod->name} berhasil dihapus.");
    }
}
