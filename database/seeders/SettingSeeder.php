<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Toko
            ['key' => 'store_name', 'value' => 'VENTRA', 'group' => 'store'],
            ['key' => 'store_tagline', 'value' => 'Belanja Mudah & Cepat', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Contoh No. 1, Kota Anda', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '08123456789', 'group' => 'store'],
            ['key' => 'store_email', 'value' => 'ventra@toko.com', 'group' => 'store'],

            // Keuangan
            ['key' => 'ppn_enabled', 'value' => '0', 'group' => 'finance'],
            ['key' => 'ppn_percentage', 'value' => '11', 'group' => 'finance'],
            ['key' => 'currency_symbol', 'value' => 'Rp', 'group' => 'finance'],

            // Struk
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja di VENTRA!', 'group' => 'receipt'],
            ['key' => 'receipt_show_logo', 'value' => '1', 'group' => 'receipt'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
