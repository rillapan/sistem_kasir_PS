<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Expense::with(['user', 'expenseCategory'])->latest();

        // Filter by date range if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Filter by category if provided
        if ($request->has('expense_category_id') && $request->expense_category_id) {
            $query->where('expense_category_id', $request->expense_category_id);
        }

        $expenses = $query->paginate(10);

        // Get total expenses
        $totalExpenses = Expense::sum('jumlah');
        $monthlyTotal = Expense::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        // Get categories for filter
        $categories = ExpenseCategory::all();

        return view('expense.index', [
            'title' => 'Data Pengeluaran',
            'active' => 'expense',
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'monthlyTotal' => $monthlyTotal,
            'categories' => $categories,
            'filters' => $request->only(['start_date', 'end_date', 'expense_category_id'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ExpenseCategory::all();

        return view('expense.create', [
            'title' => 'Tambah Pengeluaran',
            'active' => 'expense',
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'nullable|string|max:255',
            'catatan' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();

        Expense::create($validatedData);

        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = Expense::with(['user', 'expenseCategory'])->findOrFail($id);

        return view('expense.show', [
            'title' => 'Detail Pengeluaran',
            'active' => 'expense',
            'expense' => $expense
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::all();

        return view('expense.edit', [
            'title' => 'Edit Pengeluaran',
            'active' => 'expense',
            'expense' => $expense,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validatedData = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'nullable|string|max:255',
            'catatan' => 'nullable|string'
        ]);

        $expense->update($validatedData);

        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
