<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = ExpenseCategory::latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($perPage === 'all') {
            $categories = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $categories = $query->paginate($perPage);
        }

        return Inertia::render('ExpenseCategories/Index', [
            'categories' => $categories->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->back()->with('message', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $expenseCategory->update($validated);

        return redirect()->back()->with('message', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return redirect()->back()->with('message', 'Kategori pengeluaran berhasil dihapus.');
    }
}
