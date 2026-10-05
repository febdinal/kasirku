<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;

    private User $userB;

    private Product $productA;

    private Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();

        $this->actingAs($this->userA);
        $this->productA = Product::create(['name' => 'Produk A', 'sku' => 'SKU-1', 'price' => 1000, 'stock' => 10, 'unit' => 'pcs']);
        Customer::create(['name' => 'Pelanggan A']);
        Category::create(['name' => 'Kategori A', 'slug' => 'kategori-a']);
        Setting::set('store_name', 'Toko A');

        $this->actingAs($this->userB);
        $this->productB = Product::create(['name' => 'Produk X', 'sku' => 'SKU-1', 'price' => 2000, 'stock' => 10, 'unit' => 'pcs']);
        Customer::create(['name' => 'Pelanggan X']);
        Setting::set('store_name', 'Toko B');
    }

    public function test_each_user_only_sees_own_data(): void
    {
        $this->actingAs($this->userA);
        $this->assertSame(['Produk A'], Product::pluck('name')->all());
        $this->assertSame(['Pelanggan A'], Customer::pluck('name')->all());
        $this->assertSame('Toko A', Setting::get('store_name'));

        $this->actingAs($this->userB);
        $this->assertSame(['Produk X'], Product::pluck('name')->all());
        $this->assertSame(0, Category::count());
        $this->assertSame('Toko B', Setting::get('store_name'));
    }

    public function test_pages_do_not_leak_other_store_data(): void
    {
        $this->actingAs($this->userA)->get(route('products.index'))
            ->assertSee('Produk A')->assertDontSee('Produk X');

        $this->actingAs($this->userB)->get(route('customers.index'))
            ->assertSee('Pelanggan X')->assertDontSee('Pelanggan A');
    }

    public function test_cannot_access_other_store_record_by_id(): void
    {
        $this->actingAs($this->userB)->get(route('products.edit', $this->productA))->assertNotFound();
        $this->actingAs($this->userB)->put(route('products.update', $this->productA), [
            'name' => 'Hack', 'price' => 1, 'stock' => 1, 'unit' => 'pcs',
        ])->assertNotFound();
        $this->actingAs($this->userB)->delete(route('products.destroy', $this->productA))->assertNotFound();

        $this->assertSame('Produk A', Product::withoutGlobalScopes()->find($this->productA->id)->name);
    }

    public function test_cannot_sell_other_store_product_or_see_other_store_transaction(): void
    {
        $this->actingAs($this->userB)->postJson(route('pos.store'), [
            'items' => [['product_id' => $this->productA->id, 'quantity' => 1, 'price' => 1000]],
            'payment_method' => 'cash',
            'payment_amount' => 1000,
        ])->assertStatus(422);

        $this->actingAs($this->userA)->postJson(route('pos.store'), [
            'items' => [['product_id' => $this->productA->id, 'quantity' => 1, 'price' => 1000]],
            'payment_method' => 'cash',
            'payment_amount' => 1000,
        ])->assertOk();

        $transaction = Transaction::withoutGlobalScopes()->firstOrFail();
        $this->assertSame($this->userA->store->id, $transaction->store_id);
        $this->assertSame($this->userA->store->id, $transaction->items()->withoutGlobalScopes()->first()->store_id);

        $this->actingAs($this->userB)->get(route('transactions.show', $transaction))->assertNotFound();
        $this->actingAs($this->userB)->get(route('transactions.receipt', $transaction))->assertNotFound();
        $this->actingAs($this->userB)->patch(route('transactions.void', $transaction))->assertNotFound();
        $this->assertSame(0, Transaction::count());
    }

    public function test_same_sku_allowed_across_stores_but_not_within_a_store(): void
    {
        // setUp sudah membuktikan SKU-1 boleh dipakai dua toko.
        $this->actingAs($this->userB)->post(route('products.store'), [
            'name' => 'Dup', 'sku' => 'SKU-1', 'price' => 1, 'stock' => 1, 'unit' => 'pcs',
        ])->assertSessionHasErrors('sku');
    }

    public function test_cannot_assign_other_store_category(): void
    {
        $categoryA = Category::withoutGlobalScopes()->where('name', 'Kategori A')->firstOrFail();

        $this->actingAs($this->userB)->post(route('products.store'), [
            'name' => 'P', 'price' => 1, 'stock' => 1, 'unit' => 'pcs', 'category_id' => $categoryA->id,
        ])->assertSessionHasErrors('category_id');
    }

    public function test_guest_sees_no_store_data(): void
    {
        auth()->logout();
        $this->assertSame('VENTRA', Setting::get('store_name', 'VENTRA'));
        $this->assertSame(0, Product::count());
    }

    public function test_user_without_store_is_blocked(): void
    {
        $orphan = User::factory()->create();
        $orphan->store()->delete();

        $this->actingAs($orphan->fresh())->get(route('dashboard'))->assertForbidden();
    }
}
