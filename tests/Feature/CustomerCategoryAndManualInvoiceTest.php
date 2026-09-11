<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCategoryAndManualInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_customer_with_preset_and_custom_category(): void
    {
        $user = User::factory()->create();

        // Preset category: Reseller
        $res = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Toko Sejahtera',
            'category' => 'Reseller',
            'phone' => '081234567890',
        ]);
        $res->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', [
            'name' => 'Toko Sejahtera',
            'category' => 'Reseller',
        ]);

        // Custom category via 'other'
        $res2 = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Bapak VIP',
            'category' => 'other',
            'category_custom' => 'VIP Khusus',
        ]);
        $res2->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', [
            'name' => 'Bapak VIP',
            'category' => 'VIP Khusus',
        ]);
    }

    public function test_can_filter_customers_by_category(): void
    {
        $user = User::factory()->create();
        Customer::create(['name' => 'Cust A', 'category' => 'Reseller']);
        Customer::create(['name' => 'Cust B', 'category' => 'Loyal']);

        $res = $this->actingAs($user)->get(route('customers.index', ['category' => 'Reseller']));
        $res->assertStatus(200);
        $res->assertSee('Cust A');
        $res->assertDontSee('Cust B');
    }

    public function test_can_checkout_with_manual_invoice_number(): void
    {
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Kopi Robusta',
            'sku' => 'KOP-001',
            'cost_price' => 5000,
            'selling_price' => 15000,
            'stock' => 10,
            'is_active' => true,
        ]);

        PaymentMethod::firstOrCreate(['code' => 'cash'], [
            'name' => 'Tunai (Cash)',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $res = $this->actingAs($user)->postJson(route('pos.store'), [
            'invoice_number' => 'NOTA-MANUAL-777',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2, 'price' => 15000],
            ],
            'payment_method' => 'cash',
            'payment_amount' => 50000,
        ]);

        $res->assertStatus(200);
        $res->assertJson([
            'success' => true,
            'invoice_number' => 'NOTA-MANUAL-777',
        ]);

        $this->assertDatabaseHas('transactions', [
            'invoice_number' => 'NOTA-MANUAL-777',
            'total' => 30000,
        ]);
    }

    public function test_checkout_rejects_duplicate_manual_invoice_number(): void
    {
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Kopi Robusta',
            'sku' => 'KOP-001',
            'cost_price' => 5000,
            'selling_price' => 15000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Existing transaction with same invoice
        Transaction::create([
            'invoice_number' => 'DUPLICATE-INV',
            'user_id' => $user->id,
            'subtotal' => 10000,
            'total' => 10000,
            'payment_amount' => 10000,
            'payment_method' => 'cash',
        ]);

        $res = $this->actingAs($user)->postJson(route('pos.store'), [
            'invoice_number' => 'DUPLICATE-INV',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'price' => 15000],
            ],
            'payment_method' => 'cash',
            'payment_amount' => 20000,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['invoice_number']);
    }

    public function test_checkout_auto_generates_invoice_when_omitted(): void
    {
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Teh Manis',
            'sku' => 'TEH-001',
            'cost_price' => 2000,
            'selling_price' => 5000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $res = $this->actingAs($user)->postJson(route('pos.store'), [
            'invoice_number' => '',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'price' => 5000],
            ],
            'payment_method' => 'cash',
            'payment_amount' => 10000,
        ]);

        $res->assertStatus(200);
        $this->assertTrue(str_starts_with($res->json('invoice_number'), 'INV-'));
    }
}
