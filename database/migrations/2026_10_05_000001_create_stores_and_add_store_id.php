<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tabel data toko beserta unique index global yang harus menjadi per-toko. */
    private array $tables = [
        'settings' => ['settings_key_unique', 'key'],
        'categories' => ['categories_slug_unique', 'slug'],
        'products' => ['products_sku_unique', 'sku'],
        'customers' => null,
        'transactions' => ['transactions_invoice_number_unique', 'invoice_number'],
        'transaction_items' => null,
        'payment_methods' => ['payment_methods_code_unique', 'code'],
    ];

    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // Data lama dimiliki oleh user pertama.
        $owner = DB::table('users')->orderBy('id')->first();
        $storeId = null;

        if ($owner) {
            $name = DB::table('settings')->where('key', 'store_name')->value('value') ?: $owner->name;
            $storeId = DB::table('stores')->insertGetId([
                'user_id' => $owner->id, 'name' => $name, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach ($this->tables as $tableName => $unique) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            });

            if ($storeId) {
                DB::table($tableName)->update(['store_id' => $storeId]);
            }

            Schema::table($tableName, function (Blueprint $table) use ($unique) {
                if ($unique) {
                    $table->dropUnique($unique[0]);
                }

                $table->foreign('store_id')->references('id')->on('stores')->cascadeOnDelete();

                if ($unique) {
                    $table->unique(['store_id', $unique[1]]);
                }
            });

            if (! DB::table($tableName)->whereNull('store_id')->exists()) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('store_id')->nullable(false)->change();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables, true) as $tableName => $unique) {
            Schema::table($tableName, function (Blueprint $table) use ($unique) {
                $table->dropForeign(['store_id']);
                if ($unique) {
                    $table->dropUnique(['store_id', $unique[1]]);
                }
                $table->dropColumn('store_id');
                if ($unique) {
                    $table->unique($unique[1], $unique[0]);
                }
            });
        }

        Schema::dropIfExists('stores');
    }
};
