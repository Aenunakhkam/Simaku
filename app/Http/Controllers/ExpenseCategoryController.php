<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ExpenseCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search'])
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
