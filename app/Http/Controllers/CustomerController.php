<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
        }
        $customers = $query->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:100'],
            'gender'                  => ['nullable', 'in:L,P'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'is_member'               => ['nullable', 'boolean'],
            'member_discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $data['is_member'] = $request->boolean('is_member', true);
        $data['member_discount_percent'] = $data['member_discount_percent'] ?? 10;

        Customer::create($data);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:100'],
            'gender'                  => ['nullable', 'in:L,P'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'is_member'               => ['nullable', 'boolean'],
            'member_discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $data['is_member'] = $request->boolean('is_member');
        $data['member_discount_percent'] = $data['member_discount_percent'] ?? 10;

        $customer->update($data);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
