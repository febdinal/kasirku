<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::with('store')->orderByDesc('is_admin')->orderBy('name')->paginate(20);

        $storeIds = $users->pluck('store.id')->filter();
        $productCounts = Product::withoutGlobalScopes()->whereIn('store_id', $storeIds)
            ->groupBy('store_id')->selectRaw('store_id, count(*) as c')->pluck('c', 'store_id');
        $transactionCounts = Transaction::withoutGlobalScopes()->whereIn('store_id', $storeIds)
            ->groupBy('store_id')->selectRaw('store_id, count(*) as c')->pluck('c', 'store_id');

        return view('admin.users.index', compact('users', 'productCounts', 'transactionCounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'store_name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            Store::createFor($user, $validated['store_name']);
        });

        return redirect()->route('admin.users.index')->with('success', 'User dan toko "'.$validated['store_name'].'" berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)],
            'store_name' => [$user->store ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            $user->fill(['name' => $validated['name'], 'email' => $validated['email']]);

            if (! empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();

            if ($user->store && ! empty($validated['store_name'])) {
                $user->store->update(['name' => $validated['store_name']]);

                Setting::withoutGlobalScopes()
                    ->where('store_id', $user->store->id)
                    ->where('key', 'store_name')
                    ->update(['value' => $validated['store_name']]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user()) || $user->is_admin) {
            return redirect()->route('admin.users.index')->with('error', 'Akun admin tidak dapat dihapus dari sini.');
        }

        if ($user->store) {
            Product::withoutGlobalScopes()->where('store_id', $user->store->id)
                ->whereNotNull('image')->pluck('image')
                ->each(fn ($path) => Storage::disk('public')->delete($path));
        }

        // Toko beserta seluruh datanya ikut terhapus (cascade).
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User beserta toko dan seluruh datanya berhasil dihapus.');
    }
}
