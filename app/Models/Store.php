<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Buat toko untuk user beserta pengaturan & metode pembayaran bawaan.
     */
    public static function createFor(User $user, string $name): self
    {
        $store = static::create(['user_id' => $user->id, 'name' => $name]);

        $defaults = [
            ['store_name', $name, 'store'],
            ['store_tagline', '', 'store'],
            ['store_address', '', 'store'],
            ['store_phone', '', 'store'],
            ['store_email', '', 'store'],
            ['ppn_enabled', '0', 'finance'],
            ['ppn_percentage', '11', 'finance'],
            ['currency_symbol', 'Rp', 'finance'],
            ['receipt_footer', 'Terima kasih telah berbelanja di '.$name.'!', 'receipt'],
        ];

        $methods = [
            ['Tunai (Cash)', 'cash', true],
            ['Transfer Bank', 'transfer', false],
            ['QRIS', 'qris', false],
            ['Kartu Debit', 'debit', false],
            ['Kartu Kredit', 'kredit', false],
        ];

        foreach ($defaults as [$key, $value, $group]) {
            (new Setting)->forceFill([
                'store_id' => $store->id, 'key' => $key, 'value' => $value, 'group' => $group,
            ])->save();
        }

        foreach ($methods as $i => [$methodName, $code, $isCash]) {
            (new PaymentMethod)->forceFill([
                'store_id' => $store->id, 'name' => $methodName, 'code' => $code,
                'is_cash' => $isCash, 'is_active' => true, 'sort_order' => $i + 1,
            ])->save();
        }

        return $store;
    }
}
