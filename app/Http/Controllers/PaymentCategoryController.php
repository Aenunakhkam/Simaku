<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = PaymentCategory::latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
        }

        if ($perPage === 'all') {
            $categories = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $categories = $query->paginate($perPage);
        }

        return Inertia::render('PaymentCategories/Index', [
            'categories' => $categories->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monthly,one_time',
            'default_amount' => 'required|numeric|min:0',
        ]);

        PaymentCategory::create($validated);

        return redirect()->back()->with('message', 'Kategori pemasukan berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentCategory $paymentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monthly,one_time',
            'default_amount' => 'required|numeric|min:0',
        ]);

        $paymentCategory->update($validated);

        return redirect()->back()->with('message', 'Kategori pemasukan berhasil diperbarui.');
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        if ($paymentCategory->billings()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori ini tidak dapat dihapus karena sudah digunakan pada tagihan siswa.');
        }

        $paymentCategory->delete();

        return redirect()->back()->with('message', 'Kategori pemasukan berhasil dihapus.');
    }
}
