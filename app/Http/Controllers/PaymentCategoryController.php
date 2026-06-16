<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = PaymentCategory::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('PaymentCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search'])
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
        $paymentCategory->delete();

        return redirect()->back()->with('message', 'Kategori pemasukan berhasil dihapus.');
    }
}
