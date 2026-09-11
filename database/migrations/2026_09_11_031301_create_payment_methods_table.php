<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_cash')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'Tunai (Cash)', 'code' => 'cash', 'is_cash' => true, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transfer Bank', 'code' => 'transfer', 'is_cash' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'QRIS', 'code' => 'qris', 'is_cash' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kartu Debit', 'code' => 'debit', 'is_cash' => false, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kartu Kredit', 'code' => 'kredit', 'is_cash' => false, 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
