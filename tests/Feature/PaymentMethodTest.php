<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_payment_methods_in_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Metode Pembayaran');
        $response->assertSee('Tunai (Cash)');
    }

    public function test_can_add_new_payment_method(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.payment-methods.store'), [
            'name' => 'GoPay Digital',
            'code' => 'gopay',
            'is_cash' => 0,
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertDatabaseHas('payment_methods', [
            'name' => 'GoPay Digital',
            'code' => 'gopay',
            'is_cash' => false,
            'is_active' => true,
        ]);
    }

    public function test_can_toggle_payment_method_status(): void
    {
        $user = User::factory()->create();
        $pm = PaymentMethod::where('code', 'qris')->first() ?? PaymentMethod::create([
            'name' => 'QRIS',
            'code' => 'qris',
            'is_cash' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('settings.payment-methods.toggle', $pm));

        $response->assertRedirect(route('settings.index'));
        $this->assertDatabaseHas('payment_methods', [
            'id' => $pm->id,
            'is_active' => false,
        ]);
    }

    public function test_cannot_delete_default_cash_payment_method(): void
    {
        $user = User::factory()->create();
        $cashPm = PaymentMethod::where('code', 'cash')->first() ?? PaymentMethod::create([
            'name' => 'Tunai (Cash)',
            'code' => 'cash',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('settings.payment-methods.destroy', $cashPm));

        $response->assertRedirect(route('settings.index'));
        $this->assertDatabaseHas('payment_methods', [
            'id' => $cashPm->id,
            'code' => 'cash',
        ]);
    }
}
