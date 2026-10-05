<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_non_admin_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.users.store'), [])->assertForbidden();
        $this->actingAs($user)->delete(route('admin.users.destroy', $user))->assertForbidden();
    }

    public function test_admin_can_create_user_with_store_who_can_login(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Budi', 'email' => 'budi@toko.test', 'password' => 'rahasia123', 'store_name' => 'Toko Budi',
        ])->assertRedirect(route('admin.users.index'));

        $budi = User::where('email', 'budi@toko.test')->firstOrFail();
        $this->assertFalse($budi->is_admin);
        $this->assertSame('Toko Budi', $budi->store->name);
        $this->assertDatabaseHas('payment_methods', ['store_id' => $budi->store->id, 'code' => 'cash']);

        auth()->logout();
        $this->post('/login', ['email' => 'budi@toko.test', 'password' => 'rahasia123'])->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_update_user_and_reset_password(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Baru', 'email' => 'baru@toko.test', 'password' => 'passwordbaru1', 'store_name' => 'Nama Baru',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Baru', $user->name);
        $this->assertSame('Nama Baru', $user->store->name);
        $this->assertTrue(\Hash::check('passwordbaru1', $user->password));
        $this->assertDatabaseHas('settings', ['store_id' => $user->store->id, 'key' => 'store_name', 'value' => 'Nama Baru']);
    }

    public function test_admin_can_delete_user_with_all_store_data_but_not_admins(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();

        $this->actingAs($user);
        Product::create(['name' => 'P', 'price' => 1, 'stock' => 1, 'unit' => 'pcs']);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('stores', ['id' => $user->store->id]);
        $this->assertSame(0, Product::withoutGlobalScopes()->count());

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_without_store_is_redirected_to_admin_panel(): void
    {
        $admin = $this->makeAdmin();
        $admin->store()->delete();

        $this->actingAs($admin->fresh())->get(route('dashboard'))->assertRedirect(route('admin.users.index'));
        $this->actingAs($admin->fresh())->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin->fresh())->get(route('user.edit'))->assertOk();
    }
}
