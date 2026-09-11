<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('transactions')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $customers = $query->paginate(20)->withQueryString();

        $existingCategories = Customer::whereNotNull('category')->distinct()->pluck('category')->toArray();
        $categories = array_values(array_unique(array_merge(Customer::PRESET_CATEGORIES, $existingCategories)));

        return view('customers.index', compact('customers', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $category = ($request->category === 'other' && $request->filled('category_custom'))
            ? trim($request->category_custom)
            : ($request->category ?? 'Umum');

        $request->merge(['category' => $category]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $category = ($request->category === 'other' && $request->filled('category_custom'))
            ? trim($request->category_custom)
            : ($request->category ?? 'Umum');

        $request->merge(['category' => $category]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
