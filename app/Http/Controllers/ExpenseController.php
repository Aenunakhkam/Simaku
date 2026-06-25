<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Expense::with(['category', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('voucher_number', 'ilike', "%{$search}%")
                      ->orWhere('note', 'ilike', "%{$search}%");
            })
            ->when($request->date_start && $request->date_end, function ($query) use ($request) {
                $query->whereBetween('date', [$request->date_start, $request->date_end]);
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($perPage === 'all') {
            $expenses = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $expenses = $query->paginate($perPage);
        }

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses->withQueryString(),
            'filters' => $request->only(['search', 'date_start', 'date_end', 'per_page'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Expenses/Create', [
            'categories' => ExpenseCategory::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Generate Voucher Number
        $latestExpense = Expense::latest('id')->first();
        $nextId = $latestExpense ? $latestExpense->id + 1 : 1;
        $voucherNumber = 'EXP-' . date('Ym', strtotime($validated['date'])) . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $path = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('expenses', 'public');
        }

        Expense::create([
            'voucher_number' => $voucherNumber,
            'expense_category_id' => $validated['expense_category_id'],
            'user_id' => auth()->id(),
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'note' => $validated['note'] ?? null,
            'proof_file_path' => $path,
        ]);

        return redirect()->route('expenses.index')->with('message', 'Transaksi pengeluaran berhasil dicatat.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user']);
        return Inertia::render('Expenses/Show', [
            'expense' => $expense
        ]);
    }

    public function destroy(Expense $expense)
    {
        if ($expense->proof_file_path) {
            Storage::disk('public')->delete($expense->proof_file_path);
        }
        $expense->delete();

        return redirect()->back()->with('message', 'Transaksi pengeluaran berhasil dihapus.');
    }
}
